<?php

if ($postTypeObject->hierarchical) {
    include('attributes/parent.php');
}

if ('page' == $post->post_type && 0 != count(get_page_templates($post)) && get_option('page_for_posts') != $post->ID) {
    include('attributes/template.php');
}

include('attributes/order.php');
