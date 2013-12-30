laravel-auth
============

Modification of the official laravel auth package

Installation
---
Run ```composer require andheiberg/laravel-auth:1.*```

Comment out `'Illuminate\Auth\Reminders\ReminderServiceProvider',` in `providers` in `app/config/app.php`
Comment out `'Illuminate\Auth\AuthServiceProvider',` in `providers` in `app/config/app.php`
Add `'Andheiberg\Auth\Reminders\ReminderServiceProvider',` to `providers` in `app/config/app.php`
Add `'Andheiberg\Auth\AuthServiceProvider',` to `providers` in `app/config/app.php`

Comment out `'Password' => 'Illuminate\Support\Facades\Password',` in `aliases` in `app/config/app.php`
Comment out `'Auth'     => 'Illuminate\Support\Facades\Auth',` in `aliases` in `app/config/app.php`
Add `'Password' => 'Andheiberg\Auth\Facades\Password',` to `aliases` in `app/config/app.php`
Add `'Auth'     => 'Andheiberg\Auth\Facades\Auth',` to `aliases` in `app/config/app.php`

Run `php artisan auth:setup` which adds a modified `app/config/auth.php`, `app/controllers/AuthController.php` and views in `app/views/auth`.

    // Add this to your app/routes.php
    Route::controller('/', 'AuthController', [
        'getLogin' => 'login',
        'getLogout' => 'logout',
        'getRegister' => 'register',
        'getVerifyEmail' => 'verify-email',
        'getForgotPassword' => 'forgot-password',
        'getResetPassword' => 'reset-password',
    ]);

Usage
---
See: http://laravel.com/docs/security

Only andheiberg\auth doesn't throw exceptions but instead has an `$errors MessageBag` and the functino `hasErrors()`.

andheiberg\auth also has the following functions added:

    /**
     * Log the given user ID into the application.
     *
     * @param  mixed  $id
     * @param  bool   $remember
     * @return \Andheiberg\Auth\UserInterface
     */
    public function loginUsingEmail($id, $remember = false)

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  mixed  $email
     * @return bool
     */
    public function onceUsingEmail($email)

    /**
     * Register a user to the application.
     *
     * @param  array $credentials
     * @param  bool  $login
     * @return $this
     */
    public function register($credentials, $login = false)

    /**
     * Verify a users email
     *
     * @param  array $credentials
     * @param  bool  $force
     * @return $this
     */
    public function verifyEmail($credentials, $force = false)

As you can see andheiberg/auth has a email verification step when registering users. An email is sent to the registrering email with a verification link.

This email is configurable in `config/auth.php`.

andheiberg/auth also has controller and view stubs for the auth interactions.