<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\IntegrationTestCase;

/**
 * Verifies that the plugin can store and retrieve serialized prototype
 * settings the same way the prototype action does.
 */
class PluginSettingsTest extends IntegrationTestCase {

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
    public function testSetAndGetPrototypeSetting(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        if (!$plugin || !$plugin->isActive()) {
            $this->markTestSkipped('prototyper_profile plugin not active (deps may not be migrated to 7.x yet)');
        }

        $prototype = [
            'bio' => [
                'type' => 'longtext',
                'data_type' => 'metadata',
            ],
        ];

        try {
            $this->assertTrue(
                $plugin->setSetting('prototype:default', serialize($prototype))
            );

            $raw = $plugin->getSetting('prototype:default');
            $this->assertNotEmpty($raw);

            $decoded = unserialize($raw, ['allowed_classes' => false]);
            $this->assertSame($prototype, $decoded);
        } finally {
            $plugin->unsetSetting('prototype:default');
        }
    }

    /**
     * @return void
     */
    public function testRoleScopedSettings(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        if (!$plugin || !$plugin->isActive()) {
            $this->markTestSkipped('prototyper_profile plugin not active (deps may not be migrated to 7.x yet)');
        }

        try {
            $plugin->setSetting('prototype:member', serialize(['a' => 1]));
            $plugin->setSetting('prototype:admin', serialize(['b' => 2]));

            $this->assertNotEmpty($plugin->getSetting('prototype:member'));
            $this->assertNotEmpty($plugin->getSetting('prototype:admin'));
            $this->assertNotSame(
                $plugin->getSetting('prototype:member'),
                $plugin->getSetting('prototype:admin')
            );
        } finally {
            $plugin->unsetSetting('prototype:member');
            $plugin->unsetSetting('prototype:admin');
        }
    }
}
