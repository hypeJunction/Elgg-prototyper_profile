<?php

$role = get_input('role', 'default');
$prototype = hypePrototyper()->ui->buildPrototypeFromInput();

if ($prototype && elgg_set_plugin_setting("prototype:$role", serialize($prototype), 'prototyper_profile')) {
	system_message(elgg_echo('profile:prototype:success'));
} else {
	system_message(elgg_echo('profile:prototype:error'));
}

forward(REFERER);
