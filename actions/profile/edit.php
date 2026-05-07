<?php

$guid = get_input('guid');
$user = get_entity($guid);

if (!$user || !($user instanceof ElggUser) || !$user->canEdit()) {
	return elgg_error_response(elgg_echo('profile:noaccess'));
}

try {
	$action = hypePrototyper()->action->with($user, 'profile/edit');
	if ($action->validate()) {
		$result = $action->update();
	}
} catch (\hypeJunction\Exceptions\ActionValidationException $ex) {
	return elgg_error_response(elgg_echo('prototyper:validate:error'));
} catch (\Elgg\Exceptions\FileSystem\IOException $ex) {
	return elgg_error_response(elgg_echo('prototyper:io:error', [$ex->getMessage()]));
} catch (\Exception $ex) {
	return elgg_error_response(elgg_echo('prototyper:handle:error', [$ex->getMessage()]));
}

if ($result) {
	$output = '';
	if (elgg_is_xhr()) {
		$output = (string) $action->result->output;
	}

	// Notify of profile update
	elgg_trigger_event('profileupdate', $user->type, $user);

	return elgg_ok_response($output, elgg_echo('profile:saved'), $user->getURL());
}

return elgg_error_response(elgg_echo('prototyper:action:error'));
