<?php

if (!elgg_is_active_plugin('roles')) {
	return;
}

$context = elgg_extract('filter_context', $vars, 'default');
$roles = roles_get_all_roles();

foreach ($roles as $role) {
	if ($role->name == 'visitor') {
		continue;
	}

	elgg_register_menu_item('filter', [
		'name' => $role->name,
		'text' => $role->getDisplayName(),
		'href' => elgg_http_add_url_query_elements(current_page_url(), [
			'role' => $role->name,
		]),
		'selected' => $role->name == $context,
	]);
}

echo elgg_view_menu('filter', [
	'sort_by' => 'priority',
	'class' => 'elgg-tabs',
]);