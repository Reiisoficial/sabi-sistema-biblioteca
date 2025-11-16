/**
 * Adiciona o JavaScript que faz a luz acender (Carregado por todo o site).
 */
function sabi_enqueue_scripts() {
    $js_code = "
        function acenderEstante(idEstante, prateleira, coluna) {
            document.querySelectorAll('.estante-bloco').forEach(bloco => bloco.classList.remove('ativa'));
            const estante = document.getElementById(idEstante);
            if(estante){
                estante.classList.add('ativa');
                estante.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Atualiza popup com a célula correta
            const popup = document.getElementById('matriz-popup');
            popup.innerHTML = gerarMatriz(coluna, prateleira);
            popup.style.display = 'block';
            popup.style.top = (estante.offsetTop - 230) + 'px';
            popup.style.left = (estante.offsetLeft + estante.offsetWidth/2) + 'px';
        }

        function gerarMatriz(colunaAtiva, prateleiraAtiva){
            let html = '<table><tr><th></th>';
            for(let c=1;c<=5;c++) html += `<th>Coluna ${c}</th>`;
            html += '</tr>';
            for(let p=1;p<=5;p++){
                html += `<tr><th>Prateleira ${p}</th>`;
                for(let c=1;c<=5;c++){
                    const classe = (p===prateleiraAtiva && c===colunaAtiva) ? 'destacado' : '';
                    html += `<td class='${classe}'>C${c}, P${p}</td>`;
                }
                html += '</tr>';
            }
            html += '</table>';
            return html;
        }

        document.addEventListener('DOMContentLoaded', function(){
            // Hover das estantes para popup
            document.querySelectorAll('.estante-bloco').forEach(bloco => {
                bloco.addEventListener('mouseenter', function(){
                    const popup = document.getElementById('matriz-popup');
                    const prateleira = parseInt(this.getAttribute('data-prateleira'));
                    const coluna = parseInt(this.getAttribute('data-coluna'));
                    popup.innerHTML = gerarMatriz(coluna, prateleira);
                    popup.style.display = 'block';
                    popup.style.top = (this.offsetTop - 230) + 'px';
                    popup.style.left = (this.offsetLeft + this.offsetWidth/2) + 'px';
                });
                bloco.addEventListener('mouseleave', function(){
                    document.getElementById('matriz-popup').style.display = 'none';
                });
            });
        });
    ";
    wp_add_inline_script('astra-child-theme-js', $js_code);
}
add_action( 'wp_enqueue_scripts', 'sabi_enqueue_scripts' );

/**
 * Shortcode do SABI - Busca inteligente
 */
function sabi_executar_busca_sql() {
    global $wpdb;
    $termo_busca = isset($_GET['sabi_termo']) ? trim($_GET['sabi_termo']) : '';

    if (empty($termo_busca)) {
        return '<p style="font-size:1.2em;color:#333;margin-top:20px;">Sua pesquisa será exibida aqui: insira o Título ou Autor.</p>';
    }

    // 1. Sanitização e busca case-insensitive
    $termo_busca = mb_strtolower($termo_busca, 'UTF-8');
    $termo_sql = '%' . $wpdb->esc_like($termo_busca) . '%';

    // A busca usa LOWER() para ser insensível a maiúsculas/minúsculas
    $sql = "
        SELECT L.titulo, F.estante_num, F.lado, F.prateleira_num, F.coluna_num
        FROM LIVRO L
        JOIN CLASSIFICACAO_CDD C ON L.fk_codigo_cdd = C.codigo_cdd
        JOIN LOCALIZACAO_FISICA F ON C.codigo_cdd = F.fk_codigo_cdd
        WHERE LOWER(L.titulo) LIKE %s OR LOWER(L.autor) LIKE %s
    ";

    $resultados = $wpdb->get_results($wpdb->prepare($sql, $termo_sql, $termo_sql), ARRAY_A);

    if (!empty($resultados)) {
        $primeiro = $resultados[0];
        $estante = $primeiro['estante_num'];
        $prateleira = $primeiro['prateleira_num'];
        $coluna = $primeiro['coluna_num'];

        //  Formata o número da estante para ter 2 dígitos (ex: 2 -> 02) 
        $estante_formatada = sprintf('%02d', $estante);
        
        $output  = '<h3 style="color:#2F4F4F;font-family:\'PT Sans Narrow\',sans-serif;">📚 Livro Encontrado!</h3>';
        $output .= '<p class="localizacao-sabi">';
        // Exibe o número da estante normal (ex: Estante 2) para o usuário
        $output .= "Seu livro está na: <strong>Estante {$estante}, Lado {$primeiro['lado']}, Prateleira {$prateleira}, Coluna {$coluna}</strong>.</p>";
        
        // Chama a função JS com o ID formatado (ex: 'estante-02')
        $output .= "<script>document.addEventListener('DOMContentLoaded', function(){ acenderEstante('estante-{$estante_formatada}', {$prateleira}, {$coluna}); });</script>";

        return $output;
    } else {
        return '<p style="color:#CC0000;font-weight:bold;">Lamentamos, mas nada foi encontrado com o termo "<em>' . esc_html($termo_busca) . '</em>". Tente novamente.</p>';
    }
}
add_shortcode('sabi_busca_personalizada', 'sabi_executar_busca_sql');