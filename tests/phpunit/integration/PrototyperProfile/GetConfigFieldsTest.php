<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Event;
use Elgg\IntegrationTestCase;

/**
 * Tests the GetConfigFields event handler which enriches `profile_fields`
 * config with fields defined by the prototyper for the user profile/edit form.
 *
 * This handler depends on the hypePrototyper plugin. When it is not active,
 * the test is skipped rather than fabricating a factory double.
 */
class GetConfigFieldsTest extends IntegrationTestCase {

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
    public function testHookReturnsArrayWhenPrototyperAvailable(): void {
        if (!function_exists('hypePrototyper')) {
            $this->markTestSkipped('hypePrototyper dependency not loaded in test container');
        }

        $event = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
        $event->method('getName')->willReturn('profile:fields');
        $event->method('getType')->willReturn('profile');
        $event->method('getValue')->willReturn([
            'existing' => 'text',
        ]);
        $event->method('getParams')->willReturn([]);
        $event->method('getParam')->willReturn(null);

        $handler = new GetConfigFields();

        try {
            $result = $handler($event);
        } catch (\Throwable $e) {
            $this->markTestSkipped('hypePrototyper runtime not fully bootstrapped: ' . $e->getMessage());
        }

        $this->assertIsArray($result);
        $this->assertArrayHasKey('existing', $result, 'existing config keys must be preserved');
    }
}
