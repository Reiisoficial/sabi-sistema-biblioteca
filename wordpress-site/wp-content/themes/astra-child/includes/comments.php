<?php
/**
 * Template de comentários com avaliações (Astra Child)
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            printf(
                _nx('Um comentário em "%2$s"', '%1$s comentários em "%2$s"', get_comments_number(), 'comments title', 'astra-child'),
                number_format_i18n(get_comments_number()),
                '<span>' . get_the_title() . '</span>'
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
            ));
            ?>
        </ol>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply' => 'Deixe sua avaliação',
        'comment_field' => '
            <p class="comment-form-rating">
                <label for="rating">Sua nota:</label>
                <select name="rating" id="rating" required>
                    <option value="">Escolha</option>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="1">⭐</option>
                </select>
            </p>
            <p class="comment-form-comment">
                <label for="comment">Comentário</label>
                <textarea id="comment" name="comment" cols="45" rows="4" required></textarea>
            </p>',
        'label_submit' => 'Enviar Avaliação'
    ));
    ?>
</div>
