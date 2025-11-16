<?php
/**
 * sem-login.php
 * Restringe páginas específicas mostrando mensagem de acesso negado
 */

// 🔒 Bloqueia acesso às páginas restritas
function restringir_paginas_com_mensagem() {
    if (!is_user_logged_in()) {
        global $post;

        // Slugs das páginas que só usuários logados podem acessar
        $paginas_restritas = array(
            'minha-estante-pessoal',
            'sabi-connect'
        );

        if ($post && in_array($post->post_name, $paginas_restritas)) {
            // Mostra mensagem de acesso negado com cores da paleta do site
            wp_die(
                '<div style="text-align:center; font-family: Arial, sans-serif;">
                    <h2 style="color:#FFBF00; margin-bottom:15px;">Acesso Negado!</h2> <!-- Âmbar -->
                    <p style="color:#30D5C8; font-size:16px;">Você precisa estar logado para acessar esta página.</p> <!-- Turquesa -->
                    <p><a href="' . wp_login_url(get_permalink()) . '" style="color:#FFBF00; font-weight:bold; text-decoration:underline;">Clique aqui para fazer login</a></p>
                </div>',
                'Permissão Negada',
                array('response' => 403, 'back_link' => false)
            );
        }
    }
}
add_action('template_redirect', 'restringir_paginas_com_mensagem');
