<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\IntegrationTestCase;

/**
 * Verifies that the plugin's views are registered and exist on disk.
 * Does NOT render them — rendering requires the full hypePrototyper
 * runtime which is exercised by the Playwright suite.
 */
class ViewsTest extends IntegrationTestCase {

    public function up() {}
    public function down() {}

    /**
     * @return string
     */
    public function getPluginID(): string {
        return '';
    }

    /**
     * @return void
     */
    public function testViewFilesExist(): void {
        $pluginRoot = dirname(__DIR__, 4);

        $views = [
            'views/default/admin/appearance/profile_fields.php',
            'views/default/admin/appearance/profile_fields/filter.php',
            'views/default/forms/profile/edit.php',
            'views/default/profile/details.php',
        ];

        foreach ($views as $rel) {
            $this->assertFileExists($pluginRoot . '/' . $rel, "Missing view: $rel");
        }
    }

    /**
     * @return void
     */
    public function testActionFilesExist(): void {
        $pluginRoot = dirname(__DIR__, 4);
        $this->assertFileExists($pluginRoot . '/actions/profile/edit.php');
        $this->assertFileExists($pluginRoot . '/actions/profile/prototype.php');
    }

    /**
     * @return void
     */
    public function testLanguageFileParses(): void {
        $pluginRoot = dirname(__DIR__, 4);
        $strings = include $pluginRoot . '/languages/en.php';

        $this->assertIsArray($strings);
        $this->assertArrayHasKey('profile:prototype:success', $strings);
        $this->assertArrayHasKey('profile:prototype:error', $strings);
    }
}
