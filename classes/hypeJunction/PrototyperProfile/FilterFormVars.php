<?php

namespace hypeJunction\PrototyperProfile;

use Elgg\Hook;

/**
 * Add validate flag to profile form
 */
class FilterFormVars
{
    public function __invoke(Hook $hook)
    {

        $return = (array) $hook->getValue();

        $action_name = \elgg_extract('action_name', $return);
        if ($action_name == 'profile/edit') {
            $return['validate'] = true;
        }

        return $return;
    }
}
