<?php

declare(strict_types=1);

namespace BetterPostUi;

use WpUtilService\Features\Enqueue\EnqueueManager;

class App
{
    public function __construct(
        private EnqueueManager $wpEnqueue,
    ) {
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

        $this->wpEnqueue->add('css/better-post-ui.css', [], '1.0.0');
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

        foreach (['main', 'author', 'order', 'parent', 'publish-actions'] as $file) {
            $this->wpEnqueue
                ->add('js/' . $file . '.js', [], '1.0.0');
        }
    }
}
