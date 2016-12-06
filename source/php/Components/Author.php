<?php

namespace BetterPostUi\Components;

class Author
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'authorDiv'));
        add_filter('default_hidden_meta_boxes', array($this, 'alwaysShowAuthorMetabox'), 10, 2);
        add_action('wp_ajax_better_post_ui_author', array($this, 'searchAuthor'));
    }

    /**
     * Changes the metabox title of the author metabox (admin)
     * @return void
     */
    public function authorDiv()
    {
        $postType = get_post_type();
        $postTypeObject = get_post_type_object($postType);

        // Checks if authordiv should exist
        if (!post_type_supports($postType, 'author') || (!is_super_admin() && !current_user_can($postTypeObject->cap->edit_others_posts))) {
            return;
        }

        // Remove the default authordiv and add our own
        remove_meta_box('authordiv', $postType, 'normal');
        add_meta_box(
            'authordiv',
            __('Author'),
            array($this, 'authorDivContent'),
            $postType,
            'normal',
            'default'
        );
    }

    public function searchAuthor()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            return;
        }

        add_action('pre_user_query', function ($query) {
            $query->query_where = preg_replace('/\s+/', ' ', $query->query_where);
            $query->query_where = str_replace(') ) AND ( mt1.meta_key', ') ) OR ( mt1.meta_key', $query->query_where);
        });

        // Bail if missing post_id or q
        if (!isset($_POST['post_id']) || !isset($_POST['q'])) {
            wp_send_json(array(
                'error' => array('Missing post_id or q')
            ));

            wp_die();
        }

        $q = esc_attr($_POST['q']);

        // Who
        $args = apply_filters('BetterPostUi/authors', array(
            'who' => 'authors',
            'search' => '*' . $q . '*',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'first_name',
                    'value'   => $q,
                    'compare' => 'LIKE'
                ),
                array(
                    'key'     => 'last_name',
                    'value'   => $q,
                    'compare' => 'LIKE'
                )
            )
        ));

        $authors = new \WP_User_Query($args);
        $authors = (array) $authors->results;

        uasort($authors, function ($a, $b) use ($post) {
            if ($post->post_author == $a->ID) {
                return -1;
            }

            $name = array(
                'a' => trim(get_user_meta($a->ID, 'first_name', true) . ' ' . get_user_meta($a->ID, 'last_name', true)),
                'b' => trim(get_user_meta($b->ID, 'first_name', true) . ' ' . get_user_meta($b->ID, 'last_name', true))
            );

            if (empty($name['a'])) {
                $name['a'] = $a->data->display_name;
            }

            if (empty($name['b'])) {
                $name['b'] = $b->data->display_name;
            }

            return strcmp($name['a'], $name['b']);
        });

        foreach ($authors as $author) {
            $author->data->profile_image = get_field('user_profile_picture', 'user_' . $author->ID);
            $author->data->first_name = get_user_meta($author->ID, 'first_name', true);
            $author->data->last_name = get_user_meta($author->ID, 'last_name', true);
        }

        wp_send_json($authors);
        wp_die();
    }

    public function authorDivContent()
    {
        global $post;

        $currentAuthor = false;
        if ($post->post_author) {
            $currentAuthor = get_user_by('ID', $post->post_author);
        }

        include BETTERPOSTUI_TEMPLATE_PATH . 'authordiv.php';
    }

    /**
     * Display the author metabox by default
     * @param  array $hidden Hidden metaboxes before
     * @param  array $screen Screen args
     * @return array         Hidden metaboxes after
     */
    public function alwaysShowAuthorMetabox($hidden, $screen)
    {
        if ($screen->post_type != 'page') {
            return $hidden;
        }

        $hidden = array_filter($hidden, function ($item) {
            return $item != 'authordiv';
        });

        return $hidden;
    }
}
