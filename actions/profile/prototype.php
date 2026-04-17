<?php

$role = get_input('role', 'default');
$prototype = hypePrototyper()->ui->buildPrototypeFromInput();

$plugin = elgg_get_plugin_from_id('prototyper_profile');

if ($prototype && $plugin && $plugin->setSetting("prototype:$role", serialize($prototype))) {
    return elgg_ok_response('', elgg_echo('profile:prototype:success'));
}

return elgg_error_response(elgg_echo('profile:prototype:error'));
