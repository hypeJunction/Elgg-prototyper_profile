<?php
/**
 * Edit profile form

 * @uses vars['entity']
 */

echo hypePrototyper()->form->with($user, 'profile/edit')->viewBody();