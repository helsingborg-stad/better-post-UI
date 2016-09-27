<!--
    parent_id
    menu_order
-->

<div class="better-post-ui-parent-list">
    <strong><?php _e('Parent') ?></strong>
    <?php echo $pages; ?>

    <a href="#" class="button" data-action="better-post-ui-parent-show-search"><?php _e('Show search', 'better-post-ui'); ?></a>
</div>

<div class="better-post-ui-parent-search">
    <strong><?php _e('Search'); ?> <?php echo mb_strtolower(__('Parent')) ?></strong>
    <label class="screen-reader-text" for="parent_id"><?php _e('Parent') ?></label>
    <input type="search" data-action="better-post-ui-parent-search" class="widefat" placeholder="<?php _e('Sök'); ?>…">

    <a href="#" class="button" data-action="better-post-ui-parent-show-all"><?php _e('Cancel'); ?></a>
</div>
