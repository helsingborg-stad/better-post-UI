<?php

namespace BetterPostUi\Components;

class InternalLinks
{
    public function __construct()
    {
      add_action('admin_init', array($this, 'filterInternalLinkSearch'));
    }

    public function filterInternalLinkSearch()
    {
        if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action'])) {
            $actions = array(
                'menu-quick-search',
                'wp-link-ajax'
            );

            if (in_array($_POST['action'], $actions)) {
                add_filter('wp_link_query_args', array($this, 'unsupressPostsSearch'));
                add_filter('posts_search', array($this, 'limitLinkSearch'), 10, 2);
                add_filter('wp_link_query', array($this, 'updateLinkInfo'), 10, 2);
            }
        }
    }

    /**
     * Get the post type and its parents
     * @param  array $results An associative array of query results
     * @param  array $query   An array of WP_Query arguments
     * @return array
     */
    public function updateLinkInfo($results, $query)
    {
        $results = array_map(function ($result) {
            // Get post type
            $post_type = get_post_type($result['ID']);
            $obj = get_post_type_object($post_type);
            // Add post type to result info
            $result['info'] = '<strong>' . $obj->labels->singular_name . '</strong>';
            // Get post parents
            $ancestors = get_post_ancestors($result['ID']);
            $ancestors = array_reverse($ancestors);

            // Add post parents path to info string
            if (is_array($ancestors) && !empty($ancestors)) {
                $parent_string = implode(' / ', array_map(function ($ancestor) {
                  return get_the_title($ancestor);
                }, $ancestors)) . ' / '. $result['title'];

                $result['info'] = $result['info'] . ': ' . $parent_string;
            }

            return $result;
        }, $results);

        return $results;
    }

    /**
     * Limits internal link search to "post title" field
     * @param  string   $search   Search SQL for WHERE clause
     * @param  obj      $wp_query The current WP_Query object
     * @return string             Modified search string
     */
    public function limitLinkSearch($search, $wp_query)
    {
        global $wpdb;

        if (empty($search)) {
            return $search;
        }

        $query_vars = $wp_query->query_vars;
        $search = '';
        $and = '';

        foreach((array)$query_vars['search_terms'] as $term) {
            $search .= "{$searchand}(($wpdb->posts.post_title LIKE '%{$wpdb->esc_like($term)}%'))";
            $and = ' AND ';
        }

        $search = (!empty($search)) ? " AND ({$search}) " : $search;

        return $search;
    }

    /**
     * Unsupress posts search filters
     * @param  array $args Default post search args
     * @return array       Modified post search args
     */
    public function unsupressPostsSearch($args)
    {
        $args['suppress_filters'] = false;
        return $args;
    }

}
