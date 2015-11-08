<?php

/**
 * Elgg user display (details)
 * @uses $vars['entity'] The user entity
 */
$user = elgg_get_page_owner_entity();

$profile_fields = elgg_get_config('profile_fields');

echo '<div id="profile-details" class="elgg-body pll">';
echo "<span class=\"hidden nickname p-nickname\">{$user->username}</span>";
echo "<h2 class=\"p-name fn\">{$user->name}</h2>";

// the controller doesn't allow non-admins to view banned users' profiles
if ($user->isBanned()) {
	$title = elgg_echo('banned');
	$reason = ($user->ban_reason === 'banned') ? '' : $user->ban_reason;
	echo "<div class='profile-banned-user'><h4 class='mbs'>$title</h4>$reason</div>";
}

echo elgg_view("profile/status", ["entity" => $user]);

echo hypePrototyper()->profile->with($user, 'profile/edit')->filter(function(hypeJunction\Prototyper\Elements\Field $field) {
	return !in_array($field->getShortname(), ['username', 'name', 'ban_reason', 'description']);
})->view();

if ($user->description) {
	echo "<p class='profile-aboutme-title'><b>" . elgg_echo("profile:aboutme") . "</b></p>";
	echo "<div class='profile-aboutme-contents'>";
	echo elgg_view('output/longtext', ['value' => $user->description, 'class' => 'mtn']);
	echo "</div>";
}

echo '</div>';
