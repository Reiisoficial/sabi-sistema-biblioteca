<?php
// =======================================
// REGISTRAR LIVRO NA ESTANTE
// =======================================
add_action('wp_ajax_registrar_livro', 'registrar_livro_callback');
add_action('wp_ajax_nopriv_registrar_livro', 'registrar_livro_callback');

function registrar_livro_callback() {
    global $wpdb;
    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error(['message' => 'Você precisa estar logado.']);

    $tabela_estante = $wpdb->prefix . "estante_pessoal";
    $id_estante = $wpdb->get_var($wpdb->prepare("SELECT id_estante FROM $tabela_estante WHERE user_id = %d", $user_id));

    if (!$id_estante) wp_send_json_error(['message' => 'Você precisa registrar uma estante primeiro.']);

    $titulo = sanitize_text_field($_POST['titulo']);
    $autor = sanitize_text_field($_POST['autor']);

    $tabela_livro = $wpdb->prefix . "livros_estante";
    $wpdb->insert($tabela_livro, [
        'id_estante' => $id_estante,
        'titulo' => $titulo,
        'autor' => $autor
    ]);

    wp_send_json_success(['message' => 'Livro adicionado com sucesso!']);
}

// =======================================
// SALVAR ANOTAÇÃO (TEXTO + FOTO)
// =======================================
add_action('wp_ajax_salvar_anotacao', 'salvar_anotacao_callback');
add_action('wp_ajax_nopriv_salvar_anotacao', 'salvar_anotacao_callback');

function salvar_anotacao_callback() {
    global $wpdb;
    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error(['message' => 'Você precisa estar logado.']);

    $id_livro = intval($_POST['id_livro']);
    $texto = sanitize_textarea_field($_POST['anotacao_texto']);

    $foto_url = '';
    if (!empty($_FILES['foto_anotacao']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $uploaded = wp_handle_upload($_FILES['foto_anotacao'], ['test_form' => false]);
        if (!isset($uploaded['error'])) $foto_url = esc_url($uploaded['url']);
    }

    $tabela_anotacao = $wpdb->prefix . "anotacoes_livro";
    $wpdb->insert($tabela_anotacao, [
        'id_livro' => $id_livro,
        'texto' => $texto,
        'foto_url' => $foto_url
    ]);

    wp_send_json_success(['message' => 'Anotação salva com sucesso!']);
}
// Shortcode para mostrar estante virtual
add_shortcode('sabi_estante_virtual', function() {
    ob_start();
    ?>
    <div id="estante-pessoal-container">
        <h2>Minha Estante Pessoal</h2>
        <div id="estante-nome"></div>
        <div id="livros-estante"></div>

        <h3>Adicionar Livro</h3>
        <form id="form-adicionar-livro">
            <input type="text" name="titulo" placeholder="Título do livro" required>
            <input type="text" name="autor" placeholder="Autor do livro">
            <button type="submit">Adicionar</button>
        </form>

        <h3>Anotações</h3>
        <form id="form-adicionar-anotacao" enctype="multipart/form-data">
            <select id="selecionar-livro" name="id_livro" required></select>
            <textarea name="anotacao_texto" placeholder="Digite sua anotação"></textarea>
            <input type="file" name="foto_anotacao">
            <button type="submit">Salvar Anotação</button>
        </form>

        <div id="anotacoes-salvas"></div>
    </div>

    <script>
    jQuery(document).ready(function($){
        // Carrega o nome da estante e livros
        function carregarEstante() {
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", { action: "carregar_estante" }, function(r){
                if(r.success) {
                    $("#estante-nome").text("Estante: " + r.data.codinome);
                    $("#livros-estante").html("");
                    $("#selecionar-livro").html("");
                    r.data.livros.forEach(l => {
                        $("#livros-estante").append("<p data-id='"+l.id_livro+"'>"+l.titulo+" - "+l.autor+"</p>");
                        $("#selecionar-livro").append("<option value='"+l.id_livro+"'>"+l.titulo+"</option>");
                    });
                }
            });
        }

        carregarEstante();

        // Adicionar Livro
        $("#form-adicionar-livro").submit(function(e){
            e.preventDefault();
            var data = $(this).serialize() + "&action=registrar_livro";
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(r){
                alert(r.data.message);
                carregarEstante();
            });
        });

        // Adicionar Anotação
        $("#form-adicionar-anotacao").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("action", "salvar_anotacao");
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(r){ alert(r.data.message); }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
});
// Carregar estante e livros
add_action('wp_ajax_carregar_estante', 'carregar_estante_callback');
add_action('wp_ajax_nopriv_carregar_estante', 'carregar_estante_callback');

function carregar_estante_callback() {
    global $wpdb;
    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error();

    $tabela_estante = $wpdb->prefix . "estante_pessoal";
    $tabela_livro = $wpdb->prefix . "livros_estante";

    $estante = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabela_estante WHERE user_id = %d", $user_id), ARRAY_A);
    if(!$estante) wp_send_json_error();

    $livros = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tabela_livro WHERE id_estante = %d", $estante['id_estante']), ARRAY_A);

    wp_send_json_success([
        'codinome' => $estante['codinome'],
        'livros' => $livros
    ]);
}
function redirecionar_para_estante_apos_login($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        return home_url(); // ← PRA MIM USAR EM QUALQUER HOSPEDAGEM!
    }
    return $redirect_to;
}
}
add_filter('login_redirect', 'redirecionar_para_estante_apos_login', 10, 3);
// ====== SISTEMA DE AVALIAÇÕES NOS COMENTÁRIOS ======

// 1️⃣ Salvar a nota enviada no formulário de comentários
function salvar_rating_comentario($comment_id) {
    if (isset($_POST['rating']) && $_POST['rating'] != '') {
        $rating = intval($_POST['rating']);
        add_comment_meta($comment_id, 'rating', $rating);
    }
}
add_action('comment_post', 'salvar_rating_comentario');

// 2️⃣ Exibir as estrelas junto de cada comentário
function exibir_rating_no_comentario($comment_text, $comment) {
    $rating = get_comment_meta($comment->comment_ID, 'rating', true);
    if ($rating) {
        $stars = str_repeat('⭐', $rating);
        $comment_text = "<div class='comentario-rating' style='margin-bottom:6px; font-size:18px;'>$stars</div>" . $comment_text;
    }
    return $comment_text;
}
add_filter('comment_text', 'exibir_rating_no_comentario', 10, 2);

// 3️⃣ Exibir média de avaliações no topo dos comentários
function mostrar_media_de_avaliacoes() {
    global $post;
    $comentarios = get_comments(array('post_id' => $post->ID, 'status' => 'approve'));
    $total = 0;
    $count = 0;

    foreach ($comentarios as $comentario) {
        $rating = get_comment_meta($comentario->comment_ID, 'rating', true);
        if ($rating) {
            $total += intval($rating);
            $count++;
        }
    }

    if ($count > 0) {
        $media = round($total / $count, 1);
        $estrelas = str_repeat('⭐', round($media));
        echo "<div class='media-avaliacoes' style='margin-bottom:10px; font-size:20px;'>
                <strong>Média das Avaliações:</strong> $estrelas <span style='font-size:14px;'>($media / 5)</span>
              </div>";
    }
}
add_action('comment_form_before', 'mostrar_media_de_avaliacoes');
// ========== BOTÕES DE APAGAR POST E COMENTÁRIO (SOMENTE AUTOR) ==========

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// --- Mostrar botão de apagar post (somente do autor) ---
function sabi_botao_apagar_post( $content ) {
    if ( is_single() && is_user_logged_in() ) {
        global $post;
        $usuario_atual = get_current_user_id();

        if ( $usuario_atual == $post->post_author ) {
            $link_apagar = wp_nonce_url(
                admin_url( 'admin-post.php?action=sabi_apagar_post&post_id=' . $post->ID ),
                'sabi_apagar_post_' . $post->ID
            );

            $botao = "
            <div class='sabi-botao-apagar-post' style='margin-top:15px;'>
                <a href='{$link_apagar}' style='background:#d63638;color:#fff;padding:6px 10px;border-radius:5px;text-decoration:none;'>🗑️ Apagar Post</a>
            </div>";
            
            $content .= $botao;
        }
    }
    return $content;
}
add_filter( 'the_content', 'sabi_botao_apagar_post' );

// --- Ação para apagar post com segurança ---
function sabi_apagar_post() {
    if ( ! is_user_logged_in() ) {
        wp_die( 'Acesso negado.' );
    }

    $post_id = intval( $_GET['post_id'] ?? 0 );

    if ( ! $post_id || ! wp_verify_nonce( $_GET['_wpnonce'], 'sabi_apagar_post_' . $post_id ) ) {
        wp_die( 'Ação inválida.' );
    }

    $post = get_post( $post_id );

    if ( get_current_user_id() == $post->post_author ) {
        wp_delete_post( $post_id, true );
        wp_redirect( home_url() );
        exit;
    } else {
        wp_die( 'Você não tem permissão para apagar este post.' );
    }
}
add_action( 'admin_post_sabi_apagar_post', 'sabi_apagar_post' );
add_action( 'admin_post_nopriv_sabi_apagar_post', 'sabi_apagar_post' );


// --- Mostrar botão de apagar comentário (somente do autor) ---
function sabi_botao_apagar_comentario( $comment_text, $comment ) {
    $usuario = get_current_user_id();

    if ( $usuario && $usuario == $comment->user_id ) {
        $link_apagar = wp_nonce_url(
            admin_url( 'admin-post.php?action=sabi_apagar_comentario&comment_id=' . $comment->comment_ID ),
            'sabi_apagar_comentario_' . $comment->comment_ID
        );

        $botao = "<div class='sabi-botao-apagar-comentario' style='margin-top:6px;'>
            <a href='{$link_apagar}' style='background:#d63638;color:#fff;padding:4px 8px;border-radius:4px;text-decoration:none;font-size:12px;'>🗑️ Apagar Comentário</a>
        </div>";

        return $comment_text . $botao;
    }
    return $comment_text;
}
add_filter( 'comment_text', 'sabi_botao_apagar_comentario', 20, 2 );

// --- Ação para apagar comentário com segurança ---
function sabi_apagar_comentario() {
    if ( ! is_user_logged_in() ) {
        wp_die( 'Acesso negado.' );
    }

    $comment_id = intval( $_GET['comment_id'] ?? 0 );

    if ( ! $comment_id || ! wp_verify_nonce( $_GET['_wpnonce'], 'sabi_apagar_comentario_' . $comment_id ) ) {
        wp_die( 'Ação inválida.' );
    }

    $comment = get_comment( $comment_id );

    if ( get_current_user_id() == $comment->user_id ) {
        wp_delete_comment( $comment_id, true );
        wp_redirect( $_SERVER['HTTP_REFERER'] ?? home_url() );
        exit;
    } else {
        wp_die( 'Você não tem permissão para apagar este comentário.' );
    }
}
add_action( 'admin_post_sabi_apagar_comentario', 'sabi_apagar_comentario' );
add_action( 'admin_post_nopriv_sabi_apagar_comentario', 'sabi_apagar_comentario' );
function sabi_estante_impacto_total() {
    // Verifica se o usuário está logado
    if ( ! is_user_logged_in() ) {
        return '<p style="text-align: center; color: #a00;">Faça login para ver sua Estante Pessoal.</p>';
    }

    $user_id = get_current_user_id();

    // 1. QUERY: Busca todos os posts do aluno para processamento
    $args = array(
        'post_type'      => 'livro_pessoal', // SEU CPT
        'author'         => $user_id,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC'
    );
    $query = new WP_Query( $args );
    $titulos_unicos = []; // Array para controle de títulos únicos
    $lista_html = '';

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $titulo = get_the_title();
            
            // 2. Cria a lista de itens únicos e pega os campos ACF
            if ( ! isset( $titulos_unicos[$titulo] ) ) {
                $autor_livro = get_field('autor_livro'); // Chave ACF do autor
                
                // Estrutura HTML do item da lista (Card)
                $lista_html .= '<a href="' . get_permalink() . '" class="sabi-item-livro-link">';
                $lista_html .= '<h4>' . esc_html($titulo) . '</h4>';
                $lista_html .= '<p class="sabi-autor">Autor: ' . esc_html($autor_livro) . '</p>';
                $lista_html .= '</a>';
                
                $titulos_unicos[$titulo] = true; // Marca como adicionado
            }
        }
        wp_reset_postdata();
    }
    $contagem_final = count($titulos_unicos);

    // 3. MONTAGEM DA SAÍDA FINAL (Métrica, Estrutura HTML, CSS e JS)
    $output = '';
    
    // CSS de Estilo (Injetado diretamente para garantir que o Elementor o veja)
    $output .= '<style>
        .sabi-dashboard-container { max-width: 800px; margin: 40px auto; text-align: center; }
        .sabi-metrica-bloco { margin-bottom: 30px; }
        .sabi-numero { font-size: 5em; font-weight: 700; color: #0073aa; display: block; line-height: 1; }
        .sabi-texto { font-size: 1.2em; color: #555; }
        
        .sabi-botao-acordeao {
            background-color: #f0f0f0;
            color: #333;
            padding: 15px 25px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 1.2em;
            transition: 0.4s;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
            margin-top: 20px;
        }
        .sabi-botao-acordeao:hover { background-color: #ddd; }
        
        /* Corpo da lista (Escondido) */
        .sabi-corpo-lista {
            padding: 0 18px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out; /* Transição para o efeito de abrir */
            background-color: #fff;
            border: 1px solid #eee;
            border-top: none;
            border-radius: 0 0 5px 5px;
            text-align: left;
        }
        
        /* Estilo do Item da Lista */
        .sabi-item-livro-link {
            display: block;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
            text-decoration: none !important;
            color: #333;
        }
        .sabi-item-livro-link:last-child { border-bottom: none; }
        .sabi-item-livro-link:hover { background-color: #fafafa; }
        .sabi-item-livro-link h4 { margin: 0; font-size: 1em; color: #0073aa; }
        .sabi-item-livro-link .sabi-autor { margin: 0; font-size: 0.85em; color: #666; }
    </style>';

    // ESTRUTURA HTML
    $output .= '<div class="sabi-dashboard-container">';
    
    // 1. Métrica (Contagem)
    $output .= '<div class="sabi-metrica-bloco">';
    $output .= '<span class="sabi-numero">' . $contagem_final . '</span>';
    $output .= '<p class="sabi-texto">Livros Únicos Registrados</p>';
    $output .= '</div>';
    
    // 2. Botão Acordeão (Clica para ver a lista)
    $output .= '<button class="sabi-botao-acordeao">';
    $output .= 'Clique para Ver a Lista Completa (' . $contagem_final . ' Títulos)';
    $output .= '<span class="sabi-icone-seta"></span>';
    $output .= '</button>';
    
    // 3. Corpo da Lista (Onde o JS faz o trabalho)
    $output .= '<div class="sabi-corpo-lista">';
    $output .= $lista_html;
    $output .= '</div>';
    
    $output .= '</div>'; // Fecha sabi-dashboard-container

    // 4. JAVASCRIPT DE INTERAÇÃO (Adiciona a interatividade)
    $output .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var botao = document.querySelector(".sabi-botao-acordeao");
            var corpo = document.querySelector(".sabi-corpo-lista");

            if (botao && corpo) {
                botao.addEventListener("click", function() {
                    this.classList.toggle("ativo");
                    if (corpo.style.maxHeight) {
                        corpo.style.maxHeight = null;
                    } else {
                        // Define a altura para a altura total de rolagem para o efeito de "abrir"
                        // Adiciona um pequeno padding (30px) para garantir que a transição seja suave.
                        corpo.style.maxHeight = (corpo.scrollHeight + 30) + "px"; 
                    }
                });
            }
        });
    </script>';

    return $output;
}
add_shortcode( 'sabi_impacto_final', 'sabi_estante_impacto_total' );