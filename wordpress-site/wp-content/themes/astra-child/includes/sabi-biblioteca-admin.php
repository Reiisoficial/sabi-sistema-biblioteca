// =======================================
// SHORTCODE: FORMULÁRIO DE CADASTRO DE LIVROS (BIBLIOTECA)
// =======================================
function sabi_formulario_cadastro_biblioteca() {
    // Apenas para usuários logados com permissão (ex: administrador)
    if (!current_user_can('manage_options')) {
        return '<p>Acesso negado. Apenas administradores podem cadastrar livros na biblioteca.</p>';
    }

    ob_start();
    ?>
    <div id="cadastro-livro-biblioteca-container">
        <h2>Cadastro de Livro na Biblioteca</h2>
        <form id="form-cadastro-livro-biblioteca">
            <p>
                <label for="livro_titulo">Título do Livro (Obrigatório):</label>
                <input type="text" id="livro_titulo" name="livro_titulo" required style="width: 100%; padding: 8px;">
            </p>
            <p>
                <label for="livro_autor">Autor do Livro (Obrigatório):</label>
                <input type="text" id="livro_autor" name="livro_autor" required style="width: 100%; padding: 8px;">
            </p>
			<p>
                <label for="livro_isbn">ISBN (Opcional):</label>
                <input type="text" id="livro_isbn" name="livro_isbn" style="width: 100%; padding: 8px;">
            </p>
            <p>
                <label for="livro_cdd">Código CDD (Obrigatório):</label>
                <input type="text" id="livro_cdd" name="livro_cdd" required style="width: 100%; padding: 8px;">
            </p>
            <h3>Localização Física (Mapa Interativo)</h3>
            <p>
                <label for="estante_num">Estante Nº (1-23):</label>
                <input type="number" id="estante_num" name="estante_num" min="1" max="23" required style="width: 100%; padding: 8px;">
            </p>
            <p>
                <label for="lado">Lado (ESQUERDO/DIREITO):</label>
                <select id="lado" name="lado" required style="width: 100%; padding: 8px;">
                    <option value="ESQUERDO">ESQUERDO</option>
                    <option value="DIREITO">DIREITO</option>
                </select>
            </p>
            <p>
                <label for="prateleira_num">Prateleira Nº (1-5):</label>
                <input type="number" id="prateleira_num" name="prateleira_num" min="1" max="5" required style="width: 100%; padding: 8px;">
            </p>
            <p>
                <label for="coluna_num">Coluna Nº (1-5):</label>
                <input type="number" id="coluna_num" name="coluna_num" min="1" max="5" required style="width: 100%; padding: 8px;">
            </p>
            <button type="submit" style="background-color: #008CBA; color: white; padding: 10px 15px; border: none; cursor: pointer;">Cadastrar Livro</button>
            <div id="mensagem-cadastro-biblioteca" style="margin-top: 10px;"></div>
        </form>

        <hr style="margin-top: 40px; margin-bottom: 30px;">
        <h2>Excluir Livro da Biblioteca (Permanente)</h2>
        <p style="color: red; font-weight: bold;">AVISO: Esta ação é permanente e remove o livro de todas as bases de localização.</p>

        <form id="form-excluir-livro-biblioteca">
            <p>
                <label for="cdd_exclusao">Código CDD do Livro a ser Excluído (Obrigatório):</label>
                <input type="text" id="cdd_exclusao" name="cdd_exclusao" required style="width: 100%; padding: 8px;">
            </p>
            <button type="submit" style="background-color: #CC0000; color: white; padding: 10px 15px; border: none; cursor: pointer;">Excluir Livro</button>
            <div id="mensagem-exclusao-biblioteca" style="margin-top: 10px;"></div>
        </form>
        
    </div>

    <script>
    jQuery(document).ready(function($){
        // FUNÇÃO DE CADASTRO (JÁ EXISTENTE)
        $("#form-cadastro-livro-biblioteca").submit(function(e){
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]').text('Aguarde...');
            var $msg = $("#mensagem-cadastro-biblioteca").html('');
            
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: $form.serialize() + "&action=sabi_cadastrar_livro_biblioteca",
                success: function(response){
                    if(response.success){
                        $msg.html('<p style="color:green; font-weight:bold;">' + response.data.message + '</p>');
                        $form[0].reset(); // Limpa o formulário
                    } else {
                        $msg.html('<p style="color:red;">' + response.data.message + '</p>');
                    }
                },
                error: function(){
                    $msg.html('<p style="color:red;">Erro na comunicação com o servidor.</p>');
                },
                complete: function(){
                    $btn.text('Cadastrar Livro');
                }
            });
        });

        // FUNÇÃO DE EXCLUSÃO (AJAX) - MUDAMOS PARA GET/REQUEST
        $("#form-excluir-livro-biblioteca").submit(function(e){
            e.preventDefault();
            
            if (!confirm("ATENÇÃO: Tem certeza que deseja EXCLUIR permanentemente este livro? Esta ação não pode ser desfeita.")) {
                return; 
            }

            var $form = $(this);
            var $btn = $form.find('button[type="submit"]').text('Excluindo...');
            var $msg = $("#mensagem-exclusao-biblioteca").html('');
            
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "GET", // Tenta o método GET, mais tolerado pelo servidor
                data: $form.serialize() + "&action=sabi_deletar_livro_biblioteca",
                success: function(response){
                    if(response.success){
                        $msg.html('<p style="color:green; font-weight:bold;">' + response.data.message + '</p>');
                        $form[0].reset(); 
                    } else {
                        $msg.html('<p style="color:red;">' + response.data.message + '</p>');
                    }
                },
                error: function(){
                    $msg.html('<p style="color:red;">Erro na comunicação com o servidor. (Falha de Firewall)</p>');
                },
                complete: function(){
                    $btn.text('Excluir Livro');
                }
            });
        });
        
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('sabi_cadastro_livro_biblioteca', 'sabi_formulario_cadastro_biblioteca');


// =======================================
// FUNÇÃO AJAX PARA CADASTRO DE LIVROS (BIBLIOTECA)
// =======================================
function sabi_cadastrar_livro_biblioteca_callback() {
    global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permissão negada.']);
    }

    $titulo = sanitize_text_field($_POST['livro_titulo']);
    $autor = sanitize_text_field($_POST['livro_autor']);
    
    // ****** CORREÇÃO CRÍTICA AQUI: Adicionamos a variável ISBN ******
    $isbn = sanitize_text_field($_POST['livro_isbn']);
    
    $cdd = sanitize_text_field($_POST['livro_cdd']);
    $estante = intval($_POST['estante_num']);
    $lado = sanitize_text_field($_POST['lado']);
    $prateleira = intval($_POST['prateleira_num']);
    $coluna = intval($_POST['coluna_num']);

    if (empty($titulo) || empty($cdd) || $estante < 1 || $prateleira < 1 || $coluna < 1) {
        wp_send_json_error(['message' => 'Preencha todos os campos corretamente.']);
    }

    // --- 1. Garante que o CDD existe na tabela CLASSIFICACAO_CDD ---
    $tabela_cdd = "CLASSIFICACAO_CDD"; 
    $cdd_existe = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabela_cdd WHERE codigo_cdd = %s", $cdd));

    if (!$cdd_existe) {
        $wpdb->insert($tabela_cdd, ['codigo_cdd' => $cdd]);
    }

    // --- 2. Insere o Livro na Tabela LIVRO ---
    $tabela_livro = "LIVRO"; 
    $wpdb->insert($tabela_livro, [
        'titulo' => $titulo,
        'autor' => $autor,
        'isbn' => $isbn, // SALVANDO O ISBN AQUI
        'fk_codigo_cdd' => $cdd
    ]);
    
    // --- 3. Insere a Localização Física na Tabela LOCALIZACAO_FISICA ---
    $tabela_localizacao = "LOCALIZACAO_FISICA"; 
    
    $wpdb->insert($tabela_localizacao, [
        'fk_codigo_cdd' => $cdd,
        'estante_num' => $estante,
        'lado' => strtoupper($lado),
        'prateleira_num' => $prateleira,
        'coluna_num' => $coluna
    ]);

    wp_send_json_success(['message' => 'Livro da Biblioteca cadastrado com sucesso e localização registrada!']);
}
add_action('wp_ajax_sabi_cadastrar_livro_biblioteca', 'sabi_cadastrar_livro_biblioteca_callback');
// =======================================
// FUNÇÃO AJAX PARA EXCLUSÃO DE LIVROS (BIBLIOTECA)
// =======================================

// 1. REGISTRA A AÇÃO AJAX
add_action('wp_ajax_sabi_deletar_livro_biblioteca', 'sabi_deletar_livro_biblioteca_callback');

function sabi_deletar_livro_biblioteca_callback() {
    global $wpdb;

    // Apenas administradores podem excluir
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permissão negada. Apenas administradores podem excluir livros.']);
    }

    // MUDANÇA AQUI: Usa $_REQUEST para aceitar GET ou POST
    $cdd = sanitize_text_field($_REQUEST['cdd_exclusao']); 

    if (empty($cdd)) {
        wp_send_json_error(['message' => 'O Código CDD é obrigatório para a exclusão.']);
    }

    $tabela_livro = "LIVRO";
    $tabela_cdd = "CLASSIFICACAO_CDD";
    $tabela_localizacao = "LOCALIZACAO_FISICA";

    // Deleta os registros em cadeia
    $wpdb->delete($tabela_localizacao, ['fk_codigo_cdd' => $cdd]);
    $wpdb->delete($tabela_livro, ['fk_codigo_cdd' => $cdd]);
   // CÓDIGO CORRETO (sem espaço):
$wpdb->delete($tabela_cdd, ['codigo_cdd' => $cdd]);

    if ($wpdb->last_error) {
        wp_send_json_error(['message' => 'Erro no banco de dados: ' . $wpdb->last_error]);
    }

    wp_send_json_success(['message' => "Livro (CDD: $cdd), localização e classificação excluídos com sucesso!"]);
}