<?php

$guid = get_input('guid');
$user = get_entity($guid);

if (!$user || !($user instanceof ElggUser) || !$user->canEdit()) {
	register_error(elgg_echo('profile:noaccess'));
	forward(REFERER);
}

try {
	$action = hypePrototyper()->action->with($user, 'profile/edit');
	if ($action->validate()) {
		$result = $action->update();
	}
} catch (\hypeJunction\Exceptions\ActionValidationException $ex) {
	register_error(elgg_echo('prototyper:validate:error'));
	forward(REFERER);
} catch (\IOException $ex) {
	register_error(elgg_echo('prototyper:io:error', [$ex->getMessage()]));
	forward(REFERER);
} catch (\Exception $ex) {
	register_error(elgg_echo('prototyper:handle:error', [$ex->getMessage()]));
	forward(REFERER);
}

if ($result) {
	if (elgg_is_xhr()) {
		echo $action->result->output;
	}
	// Notify of profile update
	elgg_trigger_event('profileupdate', $user->type, $user);

	system_message(elgg_echo("profile:saved"));
	forward($user->getURL());
} else {
	register_error(elgg_echo('prototyper:action:error'));
	forward(REFERER);
}