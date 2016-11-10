<?php

namespace BetterPostUi\Components;

class Order
{
    public function __construct()
    {
        add_action('save_post', array($this, 'saveMenuOrder'), 10, 2);
    }

    public function saveMenuOrder($postId, $post)
    {
        if (!isset($_POST['sibling_menu_order']) || empty($_POST['sibling_menu_order'])) {
            return;
        }

        $siblingOrder = $_POST['sibling_menu_order'];
        global $wpdb;
        foreach ($siblingOrder as $postId => $menuOrder) {
            $wpdb->update(
                $wpdb->posts,
                array(
                    'menu_order' => $menuOrder
                ),
                array(
                    'ID' => $postId
                ),
                array('%d'),
                array('%d')
            );
        }

        return true;
    }
}
