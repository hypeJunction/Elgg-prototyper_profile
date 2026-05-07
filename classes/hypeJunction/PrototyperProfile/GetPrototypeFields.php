<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Hook;

/**
 * Returns prototyped fields
 */
class GetPrototypeFields {

	/**
	 * Resolve the prototyped field set for the user being edited.
	 *
	 * @param Hook $hook 'fields' hook scoped to a profile entity
	 * @return array
	 */
	public function __invoke(Hook $hook) {

		$return = $hook->getValue();
		$entity = $hook->getEntityParam();

		$role = false;
		if (\elgg_is_active_plugin('roles')) {
			$role = \roles_get_role($entity);
		}

		$role_name = $role ? $role->name : 'default';

		$plugin = \elgg_get_plugin_from_id('prototyper_profile');
		$prototype = $plugin ? $plugin->getSetting("prototype:$role_name") : null;
		if (!$prototype && $role_name != 'default' && $plugin) {
			$prototype = $plugin->getSetting('prototype:default');
		}

		if ($prototype) {
			$prototype_fields = unserialize($prototype, ['allowed_classes' => false]);
			$return = array_merge($return, (array) $prototype_fields);
		} else {
			$fields = (array) \elgg()->fields->get('user', 'user');
			$return['name'] = [
				'type' => 'name',
				'data_type' => 'attribute',
				'label' => [
					\get_current_language() => \elgg_echo('user:name:label'),
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
						\get_current_language() => \elgg_echo("profile:$shortname"),
					],
					'help' => false,
				];
			}
		}

		return $return;
	}
}
