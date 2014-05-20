<?php 

Route::group(['namespace' => 'Andheiberg\Auth\Controllers'], function()
{
	// login & logout
	Route::get('login', ['as' => 'login', 'uses' => 'SessionsController@create']);
	Route::post('login', 'SessionsController@store');
	Route::get('logout', ['as' => 'logout', 'uses' => 'SessionsController@destroy']);

	// registration
	Route::get('register', ['as' => 'register', 'uses' => 'RegistrationController@getIndex']);
	Route::post('register', 'RegistrationController@postIndex');

	// password reset
	Route::get('forgot-password', ['as' => 'password.forgot', 'uses' => 'PasswordController@getForgot']);
	Route::post('forgot-password', 'PasswordController@postForgot');
	Route::get('reset-password', ['as' => 'password.reset', 'uses' => 'PasswordController@getReset']);
	Route::post('reset-password', 'PasswordController@postReset');

	// email verification
	Route::get('resend-verification-email', ['as' => 'verification.resend', 'uses' => 'VerificationController@getSendVerificationEmail']);
	Route::post('resend-verification-email', 'VerificationController@postSendVerificationEmail');
	Route::get('verify-email', ['as' => 'verification.verify', 'uses' => 'VerificationController@getVerify']);
});