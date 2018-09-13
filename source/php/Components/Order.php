<?php

namespace BetterPostUi\Components;

class Order
{
    public function __construct()
    {
        add_action('wp_ajax_better_post_ui_order_pages', array($this, 'saveMenuOrder'));
    }

    public function saveMenuOrder()
    {
        if (!isset($_POST['jsonPageOrder']) || empty($_POST['jsonPageOrder'])) {
            wp_send_json(array('status' => false, 'message' => __e('Empty ordering details.')), 200);
            return;
        }

        $siblingOrder = json_decode(stripslashes($_POST['jsonPageOrder']));

        global $wpdb;
        foreach ($siblingOrder as $menuObject) {
            $wpdb->update(
                $wpdb->posts,
                array(
                    'menu_order' => $menuObject->orderId
                ),
                array(
                    'ID' => $menuObject->postId
                ),
                array('%d'),
                array('%d')
            );
        }

        wp_send_json(array('status' => true, 'message' => __('Page order updated.')), 200);
        return;
    }
}
