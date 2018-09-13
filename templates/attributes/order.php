<?php
$orderPages = get_posts(array(
    'posts_per_page' => -1,
    'post_status' => 'any',
    'post_type' => $post->post_type,
    'post_parent' => $post->post_parent,
    'orderby' => 'menu_order title',
    'order' => 'asc'
));
?>
<section>
    <strong><?php _e('Order') ?></strong>
    <p style="margin-top:0;"><?php _e('Drag or click the arrows to reorder. The bold marked page is the post you are currently editing.', 'better-post-ui'); ?></p>
    <ul class="better-post-ui-menu-order-list">
        <?php foreach ($orderPages as $key => $page) : ?>
        <li<?php echo $page->ID == $post->ID ? ' class="current"' : ''; ?> data-post-id="<?php echo $page->ID; ?>" data-order-id="<?php echo $page->menu_order; ?>">
            <button type="button" data-action="better-post-ui-order-up">&uarr;</button>
            <button type="button" data-action="better-post-ui-order-down">&darr;</button>
            <span><?php echo $page->post_title; ?></span>

            <!-- Mimic default behaviour -->
            <?php if ($page->ID == $post->ID) : ?>
            <input type="hidden" name="menu_order" value="<?php echo $post->menu_order; ?>">
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
