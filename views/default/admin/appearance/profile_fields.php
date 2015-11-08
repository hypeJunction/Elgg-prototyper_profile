<?php

$role = get_input('role', 'default');

if (elgg_is_active_plugin('roles')) {
	echo elgg_view('admin/appearance/profile_fields/filter', [
		'filter_context' => $role,
	]);
}

echo elgg_view_form('prototyper/edit', [
	'action' => '/action/profile/prototype',
		], [
	'action' => 'profile/edit',
	'attributes' => [
		'type' => 'user',
	],
	'params' => [
		'role' => $role,
	],
]);
