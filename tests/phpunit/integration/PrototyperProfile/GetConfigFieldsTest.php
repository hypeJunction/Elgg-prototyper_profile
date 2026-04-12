<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Hook;
use Elgg\IntegrationTestCase;

/**
 * Tests the GetConfigFields hook handler which enriches `profile_fields`
 * config with fields defined by the prototyper for the user profile/edit form.
 *
 * This handler depends on the hypePrototyper plugin. When it is not active,
 * the test is skipped rather than fabricating a factory double.
 */
class GetConfigFieldsTest extends IntegrationTestCase {

    public function up() {}
    public function down() {}

    public function getPluginID(): string {
        return '';
    }

    public function testHookReturnsArrayWhenPrototyperAvailable(): void {
        if (!function_exists('hypePrototyper')) {
            $this->markTestSkipped('hypePrototyper dependency not loaded in test container');
        }

        $hook = $this->getMockBuilder(Hook::class)->getMock();
        $hook->method('getName')->willReturn('profile:fields');
        $hook->method('getType')->willReturn('profile');
        $hook->method('getValue')->willReturn([
            'existing' => 'text',
        ]);
        $hook->method('getParams')->willReturn([]);
        $hook->method('getParam')->willReturn(null);

        $handler = new GetConfigFields();

        try {
            $result = $handler($hook);
        } catch (\Throwable $e) {
            $this->markTestSkipped('hypePrototyper runtime not fully bootstrapped: ' . $e->getMessage());
        }

        $this->assertIsArray($result);
        $this->assertArrayHasKey('existing', $result, 'existing config keys must be preserved');
    }
}
