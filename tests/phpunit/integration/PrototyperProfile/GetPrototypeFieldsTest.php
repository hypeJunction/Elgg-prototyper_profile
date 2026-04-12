<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Hook;
use Elgg\IntegrationTestCase;

/**
 * Tests the GetPrototypeFields hook handler.
 *
 * This handler returns the configured prototype for a role, or a default
 * set built from `profile_fields` when no prototype is configured.
 */
class GetPrototypeFieldsTest extends IntegrationTestCase {

    public function up() {}
    public function down() {}

    public function getPluginID(): string {
        return '';
    }

    private function buildHook(\ElggUser $user, array $value = []): Hook {
        $hook = $this->getMockBuilder(Hook::class)->getMock();
        $hook->method('getName')->willReturn('prototype');
        $hook->method('getType')->willReturn('profile/edit');
        $hook->method('getValue')->willReturn($value);
        $hook->method('getEntityParam')->willReturn($user);
        $hook->method('getParam')->willReturn(null);
        $hook->method('getParams')->willReturn(['entity' => $user]);
        return $hook;
    }

    public function testReturnsDefaultFieldsWhenNoPrototypeSetting(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        if ($plugin) {
            // Ensure clean slate so default branch executes
            $plugin->removeSetting('prototype:default');
        }

        $user = $this->createUser();

        $handler = new GetPrototypeFields();
        $result = $handler($this->buildHook($user, []));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame('name', $result['name']['type']);
        $this->assertSame('attribute', $result['name']['data_type']);
    }

    public function testIncludesProfileFieldsFromConfig(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        if ($plugin) {
            $plugin->removeSetting('prototype:default');
        }

        $profile_fields = (array) \elgg_get_config('profile_fields');
        $user = $this->createUser();

        $handler = new GetPrototypeFields();
        $result = $handler($this->buildHook($user, []));

        foreach ($profile_fields as $shortname => $input_type) {
            $this->assertArrayHasKey($shortname, $result);
            $this->assertSame('metadata', $result[$shortname]['data_type']);
            $this->assertSame($input_type, $result[$shortname]['type']);
        }
    }

    public function testReturnsSavedPrototypeWhenSet(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        if (!$plugin) {
            $this->markTestSkipped('plugin entity not available');
        }

        $prototype = [
            'custom_field' => [
                'type' => 'text',
                'data_type' => 'metadata',
            ],
        ];

        $plugin->setSetting('prototype:default', serialize($prototype));

        try {
            $user = $this->createUser();
            $handler = new GetPrototypeFields();
            $result = $handler($this->buildHook($user, []));

            $this->assertArrayHasKey('custom_field', $result);
            $this->assertSame('text', $result['custom_field']['type']);
        } finally {
            $plugin->removeSetting('prototype:default');
        }
    }
}
