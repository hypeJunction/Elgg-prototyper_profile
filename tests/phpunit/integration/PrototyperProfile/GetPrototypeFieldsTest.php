<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Event;
use Elgg\IntegrationTestCase;

/**
 * Tests the GetPrototypeFields event handler.
 *
 * This handler returns the configured prototype for a role, or a default
 * set built from `profile_fields` when no prototype is configured.
 */
class GetPrototypeFieldsTest extends IntegrationTestCase {

    public function up() {}
    public function down() {}

    /**
     * @return string
     */
    public function getPluginID(): string {
        return '';
    }

    /**
     * @param ElggUser $user
     * @param array $value
     * @return Event
     */
    private function buildHook(\ElggUser $user, array $value = []): Event {
        $event = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
        $event->method('getName')->willReturn('prototype');
        $event->method('getType')->willReturn('profile/edit');
        $event->method('getValue')->willReturn($value);
        $event->method('getEntityParam')->willReturn($user);
        $event->method('getParam')->willReturn(null);
        $event->method('getParams')->willReturn(['entity' => $user]);
        return $event;
    }

    /**
     * @return void
     */
    public function testReturnsDefaultFieldsWhenNoPrototypeSetting(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        if ($plugin) {
            // Ensure clean slate so default branch executes
            $plugin->unsetSetting('prototype:default');
        }

        $user = $this->createUser();

        $handler = new GetPrototypeFields();
        $result = $handler($this->buildHook($user, []));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame('name', $result['name']['type']);
        $this->assertSame('attribute', $result['name']['data_type']);
    }

    /**
     * @return void
     */
    public function testIncludesProfileFieldsFromConfig(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        if ($plugin) {
            $plugin->unsetSetting('prototype:default');
        }

        $profile_fields = (array) \elgg()->fields->get('user', 'user');
        $user = $this->createUser();

        $handler = new GetPrototypeFields();
        $result = $handler($this->buildHook($user, []));

        foreach ($profile_fields as $shortname => $input_type) {
            $this->assertArrayHasKey($shortname, $result);
            $this->assertSame('metadata', $result[$shortname]['data_type']);
            $this->assertSame($input_type, $result[$shortname]['type']);
        }
    }

    /**
     * @return void
     */
    public function testReturnsSavedPrototypeWhenSet(): void {
        $plugin = \elgg_get_plugin_from_id('prototyper_profile');
        if (!$plugin || !$plugin->isActive()) {
            $this->markTestSkipped('prototyper_profile plugin not active (deps may not be migrated yet)');
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
            $plugin->unsetSetting('prototype:default');
        }
    }
}
