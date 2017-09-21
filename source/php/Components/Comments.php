<?php

namespace BetterPostUi\Components;

class Comments {

	public function __construct()
	{
		add_action('restrict_manage_comments', array($this, 'commentsFilter'));
		add_action('pre_get_comments', array($this, 'queryFilter'));
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
        						'label' => $post_type_obj->label
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
}
