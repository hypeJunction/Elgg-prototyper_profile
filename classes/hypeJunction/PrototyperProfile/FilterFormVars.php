<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Event;

/**
 * Add validate flag to profile form
 */
class FilterFormVars {

	/**
	 * Inject `validate` flag into the profile/edit form vars.
	 *
	 * @param Event $event 'view_vars','input/form' event with current form vars in value
	 * @return array
	 */
	public function __invoke(Event $event) {

		$return = (array) $event->getValue();

		$action_name = \elgg_extract('action_name', $return);
		if ($action_name == 'profile/edit') {
			$return['validate'] = true;
		}

		return $return;
	}
}
