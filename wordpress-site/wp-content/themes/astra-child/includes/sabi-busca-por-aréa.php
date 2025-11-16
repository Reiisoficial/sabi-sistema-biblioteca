/**
 * Shortcode: Lista Títulos de Livros por Área (Busca por Prefixo CDD)
 */
function sabi_listar_livros_por_area() {
    global $wpdb;
    $codigo_cdd_prefixo = isset($_GET['sabi_area']) ? trim($_GET['sabi_area']) : '';

    if ($codigo_cdd_prefixo === '' || !is_numeric($codigo_cdd_prefixo)) {
        return ''; // nada se não foi selecionado
    }

    // Prefixo ex: "6%" para buscar todos os CDD que começam com 6
    $prefixo_sql = $wpdb->esc_like($codigo_cdd_prefixo) . '%';

    // ✅ Corrigido o JOIN: estava unindo L.fk_codigo_cdd = L.fk_codigo_cdd (erro)
    $sql = "
        SELECT DISTINCT L.titulo, L.autor
        FROM LIVRO L
        JOIN CLASSIFICACAO_CDD C ON L.fk_codigo_cdd = C.codigo_cdd
        WHERE C.codigo_cdd LIKE %s
        ORDER BY L.titulo ASC
    ";

    $livros = $wpdb->get_results($wpdb->prepare($sql, $prefixo_sql), ARRAY_A);

    if (empty($livros)) {
        return '<p style="margin-top: 20px; color: #CC0000; font-weight: bold;">⚠️ Nenhum livro encontrado para a área selecionada.</p>';
    }

    $output = '<div style="margin-top: 20px;">';
    $output .= '<h3>📚 Livros da Classe ' . esc_html($codigo_cdd_prefixo) . '00:</h3>';
    $output .= '<ul style="list-style: none; padding: 0;">';

    foreach ($livros as $livro) {
        $titulo_link = urlencode($livro['titulo']);
        $output .= '<li style="margin-bottom: 8px;">';
        $output .= "📖 <a href='?sabi_termo=" . $titulo_link . "' style='color: #007bff; text-decoration: none; font-weight: bold;'>";
        $output .= esc_html($livro['titulo']) . "</a> - " . esc_html($livro['autor']);
        $output .= '</li>';
    }

    $output .= '</ul></div>';
    return $output;
}
add_shortcode('sabi_listar_livros_por_area', 'sabi_listar_livros_por_area');