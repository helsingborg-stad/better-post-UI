<?php

namespace BetterPostUi\Components;

class PageAttributes
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'pageAttributesDiv'));
        add_action('wp_ajax_better_post_ui_search_parent', array($this, 'searchParent'));
    }

    public function pageAttributesDiv()
    {
        $postType = get_post_type();

        // Checks if authordiv should exist
        if (!post_type_supports($postType, 'page-attributes')) {
            return;
        }

        // Remove the default pageparentdiv and add our own
        remove_meta_box('pageparentdiv', $postType, 'normal');
        add_meta_box(
            'pageparentdiv',
            'page' == $postType ? __('Page Attributes') : __('Attributes'),
            array($this, 'pageAttributesDivContent'),
            null,
            'side',
            'default'
        );
    }

    public function pageAttributesDivContent($post)
    {
        $dropdown_args = array(
            'post_type'        => $post->post_type,
            'exclude_tree'     => $post->ID,
            'selected'         => $post->post_parent,
            'name'             => 'parent_id',
            'show_option_none' => __('(no parent)'),
            'sort_column'      => 'menu_order, post_title',
            'echo'             => 0,
        );

        $dropdown_args = apply_filters('page_attributes_dropdown_pages_args', $dropdown_args, $post);
        $pages = wp_dropdown_pages($dropdown_args);

        include BETTERPOSTUI_TEMPLATE_PATH . 'pageparentdiv.php';
    }

    public function searchParent($query = null, $postType = null)
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $query = $_POST['query'];
            $postType = $_POST['postType'];
        }

        $search = new \WP_Query(array(
            'post_type' => $postType,
            's' => $query
        ));

        if (defined('DOING_AJAX') && DOING_AJAX) {
            echo json_encode($search->posts);
            wp_die();
        }

        return $search->posts;
    }
}
