<?php

declare(strict_types=1);

namespace BetterPostUi\Components;

class Comments
{
    public function __construct()
    {
        add_action('restrict_manage_comments', array($this, 'commentsFilter'));
        add_action('pre_get_comments', array($this, 'queryFilter'));
        add_filter('manage_edit-comments_columns', array($this, 'tableColumns'));
        add_action('manage_comments_custom_column', array($this, 'tableColumnsContent'), 10, 2);
    }

    /**
     * Filter comments by post type
     * @return void
     */
    public function commentsFilter()
    {
        $post_types = get_post_types();

        foreach ($post_types as &$post_type) {
            if (post_type_supports($post_type, 'comments')) {
                $post_type_obj = get_post_type_object($post_type);
                $post_type = array(
                    'name' => $post_type_obj->name,
                    'label' => $post_type_obj->label,
                );
            } else {
                unset($post_types[$post_type]);
            }
        }

        echo '<select name="post_type"><option value="">' . __('Select post type', 'better-post-ui') . '</option>';
        foreach ($post_types as $post_type) {
            $selected = isset($_GET['post_type']) && $_GET['post_type'] == $post_type['name'] ? 'selected' : '';
            echo '<option value="' . $post_type['name'] . '" ' . $selected . '>' . $post_type['label'] . '</option>';
        }
        echo '</select>';
    }

    /**
     * Filter the wp query
     * @param  WP_Query $query
     * @return void
     */
    public function queryFilter($query)
    {
        global $pagenow;

        if (!is_admin() || !$pagenow || $pagenow !== 'edit-comments.php' || !isset($_GET['post_type']) || !$_GET['post_type']) {
            return;
        }

        $query->set('post_type', $_GET['post_type']);
    }

    /**
     * Table columns
     * @param  array $columns
     * @return array
     */
    public function tableColumns($columns)
    {
        return array(
            'cb' => '',
            'author' => __('Author'),
            'comment' => __('Comment'),
            'post_type' => __('Post type', 'better-post-ui'),
            'response' => __('Response', 'better-post-ui'),
            'date' => __('Date'),
        );
    }

    /**
     * Content for table columns
     * @param  string $column
     * @param  int $postId
     * @return void
     */
    public function tableColumnsContent($column, $postId)
    {
        if ($column == 'post_type') {
            $comment = get_comment($postId, OBJECT);
            $post_type_slug = get_post_type($comment->comment_post_ID);
            $post_type_obj = get_post_type_object($post_type_slug);
            echo $post_type_obj->label;
        }
    }
}
