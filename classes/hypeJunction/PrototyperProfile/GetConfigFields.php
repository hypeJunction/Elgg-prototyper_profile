<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Event;

/**
 * Populates the profile fields config with prototyped values
 */
class GetConfigFields {

	/**
	 * Merge prototyped profile fields into the registered fields config.
	 *
	 * @param Event $event 'profile:fields','profile' event returning the user fields registry
	 * @return array
	 */
	public function __invoke(Event $event) {

		$return = (array) $event->getValue();

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
