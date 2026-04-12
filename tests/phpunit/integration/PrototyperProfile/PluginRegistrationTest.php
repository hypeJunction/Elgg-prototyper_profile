<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\IntegrationTestCase;

/**
 * Verifies the plugin is registered and its elgg-plugin.php wiring is sane.
 */
class PluginRegistrationTest extends IntegrationTestCase {

    public function up() {}
    public function down() {}

    public function getPluginID(): string {
        return '';
    }

    public function testPluginIsInstalled(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        $this->assertNotNull($plugin, 'prototyper_profile plugin must be installed at mod/prototyper_profile');
    }

    public function testElggPluginConfigParses(): void {
        $pluginRoot = dirname(__DIR__, 4);
        $config = include $pluginRoot . '/elgg-plugin.php';

        $this->assertIsArray($config);
        $this->assertArrayHasKey('plugin', $config);
        $this->assertArrayHasKey('bootstrap', $config);
        $this->assertArrayHasKey('actions', $config);
        $this->assertArrayHasKey('hooks', $config);
    }

    public function testBootstrapClassLoads(): void {
        $this->assertTrue(class_exists(Bootstrap::class));
        $bootstrap = new \ReflectionClass(Bootstrap::class);
        $this->assertTrue($bootstrap->isSubclassOf(\Elgg\PluginBootstrap::class));
        // Elgg 4 requires load(); ensure it's defined on the class
        $this->assertTrue($bootstrap->hasMethod('load'));
        $this->assertTrue($bootstrap->hasMethod('boot'));
        $this->assertTrue($bootstrap->hasMethod('init'));
    }

    public function testHookHandlerClassesExist(): void {
        $this->assertTrue(class_exists(GetConfigFields::class));
        $this->assertTrue(class_exists(FilterFormVars::class));
        $this->assertTrue(class_exists(GetPrototypeFields::class));
    }

    public function testActionsRegisteredInConfig(): void {
        $pluginRoot = dirname(__DIR__, 4);
        $config = include $pluginRoot . '/elgg-plugin.php';

        $this->assertArrayHasKey('profile/edit', $config['actions']);
        $this->assertArrayHasKey('profile/prototype', $config['actions']);
        $this->assertSame('admin', $config['actions']['profile/prototype']['access']);
    }
}
