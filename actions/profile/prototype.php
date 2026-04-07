<?php

$role = get_input('role', 'default');
$prototype = hypePrototyper()->ui->buildPrototypeFromInput();

if ($prototype && elgg_set_plugin_setting("prototype:$role", serialize($prototype), 'prototyper_profile')) {
	elgg_register_success_message(elgg_echo('profile:prototype:success'));
} else {
	elgg_register_error_message(elgg_echo('profile:prototype:error'));
}

forward(REFERER);
