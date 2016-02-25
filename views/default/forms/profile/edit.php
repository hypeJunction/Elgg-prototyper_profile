<?php

$entity = elgg_extract('entity', $vars);
echo hypePrototyper()->form->with($entity, 'profile/edit')->viewBody();