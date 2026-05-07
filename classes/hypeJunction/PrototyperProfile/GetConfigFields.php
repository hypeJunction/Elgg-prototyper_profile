<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Hook;

/**
 * Populates the profile fields config with prototyped values
 */
class GetConfigFields {

	/**
	 * Merge prototyped profile fields into the registered fields config.
	 *
	 * @param Hook $hook 'config' hook returning the user fields registry
	 * @return array
	 */
	public function __invoke(Hook $hook) {

		$return = (array) $hook->getValue();

		$user = \hypePrototyper()->entityFactory->build(['type' => 'user']);
		$fields = \hypePrototyper()->prototype->fields($user, 'profile/edit');

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
}
