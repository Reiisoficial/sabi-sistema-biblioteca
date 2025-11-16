// Processa o formulário do SABi Connect (SEM NONCE para evitar erro)
function sabi_connect_form_handler() {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $nome  = sanitize_text_field($_POST['nome']);
    $email = sanitize_email($_POST['email']);
    $area  = sanitize_text_field($_POST['area']);

    // Link do servidor Discord
    $link_servidor = 'https://discord.gg/tXmTQ8j5sz';

    // Assunto
    $assunto = "🎓 Bem-vindo ao SABi Connect! Próximos passos...";

    // Headers HTML
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Corpo HTML do e-mail
    $mensagem_html = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <h2 style='color:#007bff;'>Olá $nome,</h2>

            <p>Você está entrando na comunidade de <strong>$area</strong> do SABi Connect! 📚</p>

            <p>Siga estes passos para entrar no Discord:</p>

            <ol>
                <li><b>Clique no link:</b> <a href='$link_servidor'>$link_servidor</a></li>
                <li>Acesse o canal <b>#escolha-sua-área</b> e clique no emoji da sua área.</li>
                <li>Pronto! Você entrará automaticamente nos canais de <strong>$area</strong>.</li>
            </ol>

            <p><b>Dica:</b> verifique o spam caso o e-mail não chegue.</p>

            <p>Nos vemos lá! 🚀<br>Equipe SABi Connect</p>
        </div>
    ";

    // Envio do e-mail
    wp_mail($email, $assunto, $mensagem_html, $headers);

    // Redireciona para a página confirmando envio
    wp_redirect(home_url('/sabi-connect/?sabi_ok=1'));
    exit;
}
add_action('admin_post_nopriv_sabi_connect_form', 'sabi_connect_form_handler');
add_action('admin_post_sabi_connect_form', 'sabi_connect_form_handler');
