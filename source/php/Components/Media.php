<?php

declare(strict_types=1);

namespace BetterPostUi\Components;

class Media
{
    public function __construct()
    {
        add_filter('post_mime_types', array($this, 'customMimeTypes'));
    }

    /**
     * Add custom sortable media mime type
     * @param  array $post_mime_types Default list of post mime types
     * @return array                  Modified list
     */
    public function customMimeTypes($post_mime_types)
    {
        $post_mime_types['application/pdf'] = array(__('PDF'), __('Manage PDF', 'better-post-ui'), _n_noop('PDF <span class="count">(%s)</span>', 'PDF <span class="count">(%s)</span>'));
        return $post_mime_types;
    }
}
