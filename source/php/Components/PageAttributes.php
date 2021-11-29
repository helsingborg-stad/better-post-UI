<?php

namespace BetterPostUi\Components;

class PageAttributes
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'pageAttributesDiv'));
        add_action('wp_ajax_better_post_ui_search_parent', array($this, 'searchParent'));

        add_action('init', array($this, 'removePageAttributeSupport'));
    }

    /**
     * Remove page attribute in new editor.
     *
     * @return void
     */
    public function removePageAttributeSupport()
    {
        $postTypes = get_post_types();
        if (is_array($postTypes) && !empty($postTypes)) {
            foreach ($postTypes as $postType) {
                remove_post_type_support($postType, 'page-attributes');
            }
        }
    }

    public function pageAttributesDiv()
    {
        global $wp_meta_boxes;

        $postType = get_post_type();

        //Show only on posts that are hierarchical and public
        $enabledPostTypes = array();
        foreach ((array) get_post_types(array('hierarchical' => true), 'object') as $postTypeLoop) {
            if ($postTypeLoop->public === true) {
                $enabledPostTypes[] = $postTypeLoop->name;
            }
        }

        //Allow to filter on new post types
        if (has_filter('BetterPostUi/PageAttributes/EnabledPostTypes')) {
            $enabledPostTypes = apply_filters('BetterPostUi/PageAttributes/EnabledPostTypes', $enabledPostTypes);
        }

        //Test if this should be shown
        if (!in_array($postType, $enabledPostTypes)) {
            return;
        }

        add_meta_box(
            'pageparentdiv',
            'page' == $postType ? __('Page Attributes') : __('Attributes'),
            array($this, 'pageAttributesDivContent'),
            $postType,
            'side',
            'default'
        );
    }

    /**
     * Check if array has key recursivly
     * @param  string  $needle   Array key to find
     * @param  array   $haystack Array to search
     * @return boolean
     */
    public function hasKey(string $needle, array $haystack): bool
    {
        foreach ($haystack as $key => $value) {
            if ($key === $needle) {
                return true;
            }

            if (is_array($value)) {
                if ($x = $this->hasKey($needle, $value)) {
                    return $x;
                }
            }
        }

        return false;
    }

    /**
     * The contents of the pageattributes div
     * @param  object $post Current post object
     * @return void
     */
    public function pageAttributesDivContent($post)
    {
        $postTypeObject = get_post_type_object($post->post_type);
        include BETTERPOSTUI_TEMPLATE_PATH . 'pageparentdiv.php';
    }

    /**
     * Search parent
     * @param  string $query    Search query
     * @param  string $postType Post type
     * @return array            Found posts
     */
    public function searchParent($query = null, $postType = null)
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $query = $_POST['query'];
            $postType = $_POST['postType'];
        }

        $search = new \WP_Query(array(
            'post_type' => $postType,
            'post_status' => array('publish', 'future', 'draft', 'pending', 'private'),
            's' => $query
        ));

        if (defined('DOING_AJAX') && DOING_AJAX) {
            echo json_encode($search->posts);
            wp_die();
        }

        return $search->posts;
    }
}
