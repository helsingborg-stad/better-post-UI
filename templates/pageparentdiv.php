<?php

if ($postTypeObject->hierarchical) {
    include('attributes/parent.php');
}

if (count(get_page_templates($post)) > 0 && get_option('page_for_posts') != $post->ID && !get_current_screen()->is_block_editor()) {
    include('attributes/template.php');
}

if (get_current_screen()->action != 'add' && $postTypeObject->hierarchical) {
    include('attributes/order.php');
}
