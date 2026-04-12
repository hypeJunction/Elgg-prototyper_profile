<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Hook;
use Elgg\IntegrationTestCase;

/**
 * Tests the FilterFormVars hook handler.
 *
 * Adds `validate => true` to view vars for the `input/form` view when
 * the form's action_name is `profile/edit`.
 */
class FilterFormVarsTest extends IntegrationTestCase {

    public function up() {}
    public function down() {}

    public function getPluginID(): string {
        return '';
    }

    private function buildHook(array $value): Hook {
        $hook = $this->getMockBuilder(Hook::class)->getMock();
        $hook->method('getName')->willReturn('view_vars');
        $hook->method('getType')->willReturn('input/form');
        $hook->method('getValue')->willReturn($value);
        $hook->method('getParams')->willReturn([]);
        $hook->method('getParam')->willReturn(null);
        return $hook;
    }

    public function testSetsValidateFlagForProfileEdit(): void {
        $handler = new FilterFormVars();
        $result = $handler($this->buildHook([
            'action_name' => 'profile/edit',
        ]));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('validate', $result);
        $this->assertTrue($result['validate']);
    }

    public function testDoesNotSetValidateForOtherForms(): void {
        $handler = new FilterFormVars();
        $result = $handler($this->buildHook([
            'action_name' => 'something/else',
        ]));

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('validate', $result);
    }

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
