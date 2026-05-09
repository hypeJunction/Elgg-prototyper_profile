<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Event;
use Elgg\IntegrationTestCase;

/**
 * Tests the FilterFormVars event handler.
 *
 * Adds `validate => true` to view vars for the `input/form` view when
 * the form's action_name is `profile/edit`.
 */
class FilterFormVarsTest extends IntegrationTestCase {

    public function up() {}
    public function down() {}

    /**
     * @return string
     */
    public function getPluginID(): string {
        return '';
    }

    /**
     * @param array $value
     * @return Event
     */
    private function buildHook(array $value): Event {
        $event = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
        $event->method('getName')->willReturn('view_vars');
        $event->method('getType')->willReturn('input/form');
        $event->method('getValue')->willReturn($value);
        $event->method('getParams')->willReturn([]);
        $event->method('getParam')->willReturn(null);
        return $event;
    }

    /**
     * @return void
     */
    public function testSetsValidateFlagForProfileEdit(): void {
        $handler = new FilterFormVars();
        $result = $handler($this->buildHook([
            'action_name' => 'profile/edit',
        ]));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('validate', $result);
        $this->assertTrue($result['validate']);
    }

    /**
     * @return void
     */
    public function testDoesNotSetValidateForOtherForms(): void {
        $handler = new FilterFormVars();
        $result = $handler($this->buildHook([
            'action_name' => 'something/else',
        ]));


        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('validate', $result);
    }

    /**
     * @return void
     */
    public function testPreservesExistingVars(): void {
        $handler = new FilterFormVars();
        $result = $handler($this->buildHook([
            'action_name' => 'profile/edit',
            'existing_key' => 'existing_value',
        ]));

        $this->assertSame('existing_value', $result['existing_key']);
        $this->assertTrue($result['validate']);
    }
}
