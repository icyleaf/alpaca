<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	'username' => array(
		'invalid'			=> __('Incorrect Login or Password'),
		'not_empty'			=> __('Login must not be empty'),
		'available'			=> __('Login has been registered'),
		'not_actived'		=> __('Nonactivated Login, check the mail to active.'),
		'not_exists'		=> __('Record not found'),
		'alpha_numeric'     => __('Incorrect Username'),
	),
	'email' => array(
		'default' 			=> __('Email does not match the required format'),
		'invalid'			=> __('Incorrect Email'),
		'not_empty'			=> __('Email must not be empty'),
		'max_length'		=> __('Email must be less than :param1 characters long'),
		'email_available'	=> __('Login has been registered'),
		'unregistered'		=> __('Email is not register'),
		'not_actived'		=> __('Nonactivated Email, check the mail to active.'),
		'not_exists'		=> __('Record not found'),
	),
	'nickname' => array(
		'not_empty'			=> __('Nickname must not be empty'),
		'min_length'		=> __('Nickname must be at least :param1 characters long'),
		'max_length'		=> __('Nickname must be less than :param1 characters long'),
		'default'			=> __('Incorrect Nickname'),
	),
	'current_password' => array(
		'not_empty'			=> __('Current Password must not be empty'),
		'min_length'		=> __('Password must be at least :param1 characters long'),
		'max_length'		=> __('Password must be less than :param1 characters long'),
		'invalid'			=> __('Incorrect Password'),
	),
	'password' => array(
		'not_empty'			=> __('Password must not be empty'),
		'min_length'		=> __('Password must be at least :param1 characters long'),
		'max_length'		=> __('Password must be less than :param1 characters long'),
		'invalid'			=> __('Incorrect Password'),
	),
	'password_confirm' => array(
		'default'			=> __('Password must be the same as :param1'),
		'min_length'		=> __('Password must be at least :param1 characters long'),
		'max_length'		=> __('Password must be less than :param1 characters long'),
	),
	'hash_code' => array(
		'not_empty'			=> __('Validation code must not be empty'),
		'invalid'			=> __('Invalid Validation code. Do you pass the verify or typo?') ,
	),
	'name' => array(
		'not_empty'			=> __(':field must not be empty'),
	),
	'title' => array(
		'not_empty'			=> __('Title must not be empty'),
	),
	'desc' => array(
		'not_empty'			=> __(':field must not be empty'),
	),
	'content' => array(
		'not_empty'			=> __('Content must not be empty'),
	),
	'author' => array(
		'not_empty'			=> __(':field must not be empty'),
	),
	'gtalk'  => array(
		'email'             => __('Incorrect Email'),
	),
	'msn'   => array(
		'email'             => __('Incorrect Email'),
	),
);