<?php

namespace BetterPostUi;

class App
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));

        new \BetterPostUi\Components\Author();
        new \BetterPostUi\Components\PageAttributes();
        new \BetterPostUi\Components\Order();
        new \BetterPostUi\Components\InternalLinks();
        new \BetterPostUi\Components\Comments();
        new \BetterPostUi\Components\Media();
    }

    public function isEditPage()
    {
        global $pagenow;

        if (!is_admin()) {
            return false;
        }

        if (get_post_type() === 'attachment') {
            return false;
        }

        return in_array($pagenow, array('post.php', 'post-new.php'));
    }

    /**
     * Enqueue required style
     * @return void
     */
    public function enqueueStyles()
    {
        if (!$this->isEditPage()) {
            return;
        }

        wp_enqueue_style('better-post-ui', BETTERPOSTUI_URL . '/dist/'
        .\BetterPostUi\Helper\CacheBust::name('css/better-post-ui.css'),
        array(), '1.0.0');
    }

    /**
     * Enqueue required scripts
     * @return void
     */
    public function enqueueScripts()
    {
        if (!$this->isEditPage()) {
            return;
        }

        foreach(['main', 'author', 'order', 'parent', 'publish-actions'] as $file) {
            wp_enqueue_script('better-post-ui-' . $file, BETTERPOSTUI_URL . '/dist/'
            .\BetterPostUi\Helper\CacheBust::name('js/' . $file . '.js'),
            array(), 
            '1.0.0', [
                'in_footer' => true,
            ]);
        }
    }
}
