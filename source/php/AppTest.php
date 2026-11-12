<?php

declare(strict_types=1);

namespace {
    function add_action($hook, $callback, $priority = 10, $acceptedArgs = 1): bool
    {
        $GLOBALS['betterPostUiTestHooks'][$hook][] = $callback;
        return true;
    }

    function add_filter($hook, $callback, $priority = 10, $acceptedArgs = 1): bool
    {
        return true;
    }

    function is_admin(): bool
    {
        return $GLOBALS['betterPostUiTestAdmin'];
    }

    function get_post_type(): string
    {
        return $GLOBALS['betterPostUiTestPostType'];
    }

    function plugins_url($path = '', $plugin = ''): string
    {
        return 'https://example.test/wp-content/plugins/better-post-ui';
    }

    function wp_cache_get($key, $group = '', $force = false, &$found = null): array
    {
        return [];
    }

    function wp_register_style(...$args): bool
    {
        $GLOBALS['betterPostUiTestAssets']['styles'][] = $args;
        return true;
    }

    function wp_enqueue_style(...$args): void
    {
        $GLOBALS['betterPostUiTestAssets']['enqueuedStyles'][] = $args[0];
    }

    function wp_register_script(...$args): bool
    {
        $GLOBALS['betterPostUiTestAssets']['scripts'][] = $args;
        return true;
    }

    function wp_enqueue_script(...$args): void
    {
        $GLOBALS['betterPostUiTestAssets']['enqueuedScripts'][] = $args[0];
    }
}

namespace BetterPostUi {
    use PHPUnit\Framework\TestCase;
    use WpService\Implementations\NativeWpService;
    use WpUtilService\WpUtilService;

    final class AppTest extends TestCase
    {
        protected function setUp(): void
        {
            $GLOBALS['betterPostUiTestHooks'] = [];
            $GLOBALS['betterPostUiTestAssets'] = [
                'styles' => [],
                'scripts' => [],
                'enqueuedStyles' => [],
                'enqueuedScripts' => [],
            ];
            $GLOBALS['betterPostUiTestAdmin'] = true;
            $GLOBALS['betterPostUiTestPostType'] = 'post';
            $GLOBALS['pagenow'] = 'post.php';
        }

        public function testEnqueuesEditorAssetsThroughNativeWpService(): void
        {
            $util = new WpUtilService(new NativeWpService());
            new App($util->enqueue('/var/www/wp-content/plugins/better-post-ui'));

            foreach ($GLOBALS['betterPostUiTestHooks']['admin_enqueue_scripts'] as $callback) {
                $callback();
            }

            $assets = $GLOBALS['betterPostUiTestAssets'];
            self::assertSame(['css-better-post-uicss'], $assets['enqueuedStyles']);
            self::assertSame(
                ['js-mainjs', 'js-authorjs', 'js-orderjs', 'js-parentjs', 'js-publish-actionsjs'],
                $assets['enqueuedScripts'],
            );
            self::assertSame(
                'https://example.test/wp-content/plugins/better-post-ui/assets/dist/css/better-post-ui.css',
                $assets['styles'][0][1],
            );
            self::assertSame(
                'https://example.test/wp-content/plugins/better-post-ui/assets/dist/js/main.js',
                $assets['scripts'][0][1],
            );
            self::assertTrue($assets['scripts'][0][4]);
        }

        public function testDoesNotEnqueueAssetsOnAttachmentEditor(): void
        {
            $GLOBALS['betterPostUiTestPostType'] = 'attachment';
            $util = new WpUtilService(new NativeWpService());
            new App($util->enqueue('/var/www/wp-content/plugins/better-post-ui'));

            foreach ($GLOBALS['betterPostUiTestHooks']['admin_enqueue_scripts'] as $callback) {
                $callback();
            }

            self::assertSame([], $GLOBALS['betterPostUiTestAssets']['styles']);
            self::assertSame([], $GLOBALS['betterPostUiTestAssets']['scripts']);
        }
    }
}
