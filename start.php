<?php

/**
 * AJAX tabs
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'prototyper_profile_init');

/**
 * Initialize the plugin
 * @return void
 */
function prototyper_profile_init() {

	elgg_register_action('profile/prototype', __DIR__ . '/actions/profile/prototype.php', 'admin');
	elgg_register_action('profile/edit', __DIR__ . '/actions/profile/edit.php');

	elgg_register_plugin_hook_handler('prototype', 'profile/edit', 'prototyper_profile_get_prototype_fields');

	elgg_get_plugin_setting('profile:fields', 'profile', 'prototyper_profile_get_config_fields');
}

/**
 * Returns prototyped fields
 *
 * @param string $hook   "prototype"
 * @param string $type   "profile/edit"
 * @param array  $return Fields
 * @param array  $params Hook params
 * @return array
 */
function prototyper_profile_get_prototype_fields($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	$role = false;
	if (elgg_is_active_plugin('roles')) {
		$role = roles_get_role($entity);
	}

	$role_name = $role ? $role->name : 'default';
	$prototype = elgg_get_plugin_setting("prototype:$role_name", 'prototyper_profile');
	if (!$prototype && $role_name != 'default') {
		$prototype = elgg_get_plugin_setting('prototype:default', 'prototyper_profile');
	}

	if ($prototype) {
		$prototype_fields = unserialize($prototype);
		$return = array_merge($return, $prototype_fields);
	} else {
		$fields = elgg_get_config('profile_fields');
		$return['name'] = [
			'type' => 'name',
			'data_type' => 'attribute',
			'label' => [
				get_current_language() => elgg_echo('user:name:label'),
			],
			'help' => false,
			'validation_rules' => [
				'maxlength' => 50,
			],
		];
		foreach ($fields as $shortname => $input_type) {
			$return[$shortname] = [
				'type' => $input_type,
				'data_type' => 'metadata',
				'label' => [
					get_current_language() => elgg_echo("profile:$shortname"),
				],
				'help' => false,
			];
		}
	}

	return $return;
}

/**
 * Populates the profile fields config with prototyped values
 *
 * @param string $hook   "prototype"
 * @param string $type   "profile/edit"
 * @param array  $return Fields
 * @param array  $params Hook params
 * @return array
 */
function prototyper_profile_get_config_fields($hook, $type, $return, $params) {

	$user = hypePrototyper()->entityFactory->build(['type' => 'user']);
	$fields = hypePrototyper()->prototype->fields($user, 'profile/edit');

	foreach ($fields as $field) {
		/* @var $field \hypeJunction\Prototyper\Elements\Field */

		if ($field->getDataType() !== 'metadata') {
			// only add metadata fields
			continue;
		}

		$shortname = $field->getShortname();
		if (!array_key_exists($shortname, $return)) {
			$return[$shortname] = $field->getType();
		}
	}

	return $return;
}
