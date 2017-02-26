<?php $template = !empty($post->page_template) ? $post->page_template : false; global $post; ?>

<section class="better-post-ui-template">
    <strong><?php _e('Template', 'better-post-ui') ?></strong>
    <?php
    /**
     * Fires immediately after the heading inside the 'Template' section
     * of the 'Page Attributes' meta box.
     *
     * @since 4.4.0
     *
     * @param string  $template The template used for the current post.
     * @param WP_Post $post     The current post.
     */
    do_action( 'page_attributes_meta_box_template', $template, $post );
    ?>
    <label class="screen-reader-text" for="page_template"><?php _e('Page Template', 'better-post-ui') ?></label>
    <select name="page_template" id="page_template">
    <?php
    /**
     * Filters the title of the default page template displayed in the drop-down.
     *
     * @since 4.1.0
     *
     * @param string $label   The display value for the default page template title.
     * @param string $context Where the option label is displayed. Possible values
     *                        include 'meta-box' or 'quick-edit'.
     */
    $default_title = apply_filters( 'default_page_template_title',  __( 'Default Template', 'better-post-ui' ), 'meta-box' );
    ?>
    <option value="default"><?php echo esc_html( $default_title ); ?></option>
    <?php page_template_dropdown($template, $post->post_type); ?>
    </select>
</section>

