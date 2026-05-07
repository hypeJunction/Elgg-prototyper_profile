<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Hook;

/**
 * Add validate flag to profile form
 */
class FilterFormVars {

	/**
	 * Inject `validate` flag into the profile/edit form vars.
	 *
	 * @param Hook $hook 'form:prepare:fields' hook with current form vars in value
	 * @return array
	 */
	public function __invoke(Hook $hook) {

		$return = (array) $hook->getValue();

		$action_name = \elgg_extract('action_name', $return);
		if ($action_name == 'profile/edit') {
			$return['validate'] = true;
		}

		return $return;
	}
}
