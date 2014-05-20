Auth
===
Extention of the illuminate/auth package for laravel.

The auth package is great, but it is perhaps a little to simplistic. I've tried to add the most essential functions to the package without bloading it.

This package builds upon the great foundation and adds:

- [Email verification](#email-verification) (click a link in an email to verify your email)
- [Events](#events)
- [Plug-n-Play](#plug-n-play) (views, routes, controllers, etc.)
- [Move to exceptions](#exceptions)
- [More fine grained validation feedback (better UX)](#validation)


## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Email verification](#email-verification)
    - [Events](#events)
    - [Plug-n-Play](#plug-n-play)
    - [Exceptions](#exceptions)
    - [Validation](#validation)

---

## Installation

Add the package to your composer project.

`composer require andheiberg/auth:2.*`

Register the service provider in `app/config/app.php`.

```
// 'Illuminate\Auth\AuthServiceProvider',
// 'Illuminate\Auth\Reminders\ReminderServiceProvider',

'Andheiberg\Auth\AuthServiceProvider',
```

Add the alias to the list of aliases in `app/config/app.php`.

```
// 'Auth'            => 'Illuminate\Support\Facades\Auth',
// 'Password'        => 'Illuminate\Support\Facades\Password',

'Auth'            => 'Andheiberg\Auth\Facades\Auth',
'Password'        => 'Andheiberg\Auth\Facades\Password',
'AuthVerify'      => 'Andheiberg\Auth\Facades\AuthVerify',
```

You also need to make your model implement UserInterface and RemindableInterface. If you are using eloquent you can simply pull in the respective traits. 

```
use Andheiberg\Auth\UserTrait;
use Andheiberg\Auth\UserInterface;
use Andheiberg\Auth\Reminders\RemindableTrait;
use Andheiberg\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

}
```

## Configuration

The packages provides you with some configuration options.

To create the configuration file run this command in your command line app:

```bash
$ php artisan config:publish andheiberg/auth
```

The configuration file will be published here: `app/config/packages/andheiberg/auth/config.php`.

## Usage

The original auth interface has not been changed, and for that reason, you can always use [the laravel docs for auth](http://laravel.com/docs/security).

I've added a `Auth::register()` command to make registration easier and more obvious.

This also lets providers implement password hashing on registration, so it's not in the controller or model.

```
/**
	 * Register a new user using the given credentials.
	 *
	 * @param  array  $credentials
	 * @param  bool   $verify
	 * @param  bool   $login
	 * @param  bool   $remember
	 * @return bool
	 */
	public function register(array $credentials = array(), $verify = true, $login = false, $remember = false);
```

The following additions should also be considered though.

### Email verification
Fake or incorrectly typed emails are a problem if you use transactional emails. Additionally you will mostlikly want to create a barrier for spammers, so they can't easily create infinite accounts.

The most common way of solving this is by sending an verification email to new account holders after they signup. The email contains a unique link they need to click to verify their email address.

This has been added to the signup flow.

You can overwrite the email view in `app/views/packages/auth/emails/verification.blade.php` (see [Plug-n-Play](#plug-n-play))

`AuthVerify::remind()` and `AuthVerify::verify()` can be used to resend the email and verify an email with the sent token.

```
	/**
	 * Send a verification reminder to a user.
	 *
	 * @param  array    $credentials
	 * @param  Closure  $callback
	 * @return string
	 */
	public function remind(array $credentials, Closure $callback = null)

	/**
	 * Verify the email for the given token.
	 *
	 * @param  array    $credentials
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function verify(array $credentials)
```

### Events
Registration flow is nutroiously complex and specific to your application. You might want to send a welcome email or queue a complicated job related to new users.

In order to facilitate this I've added a series of events that you can listen for to add your application specific logic.

- `auth.register`
- `auth.registered`
- `auth.verify`
- `auth.verified`
- `auth.login`
- `auth.loggedin`
- `auth.resetting-password`
- `auth.password-reset`

### Plug-n-Play
Authentication is needed on most projects. Copy-pasting or rewriting every related view, controller, localization file and route is time consuming and wasteful.

Auth will work out of the box. The package has everything you need to get started, yet everything is custimizable. You can disable the routes and write your own controllers, views, routes, etc.

The routes can be disabled in the config file.

You can then create your own controllers from scratch or use ours as a starting point.

To move our controllers to your `app/controllers` use `php artisan auth:controllers`.

To move our views to your `app/views` use `php artisan view:publish andheiberg/auth`.

To move our localization files to your `app/lang` use `php artisan auth:lang`.

### Exceptions
The original auth package did not use exceptions for error handling, but a mix of boolean returns and return codes.

I find exceptions to be more clear and easier to understand and codearound.

However I understand the need for convenience, and catching 100 differnt `AuthExceptions` while empowering, can be tedius. So I've added a `messages()` function to `AuthException`, that return a array representation of the validation error. This is powerful combined with RedirectResponse's `withErrors()`:

```
try
{
	$credentials = Input::only(['email', 'password']);

	Auth::attempt($credentials, true);

	return Redirect::intended(route('dashboard'));
}
catch (AuthException $e)
{
	return Redirect::back()->withInput()->withErrors($e->messages());
}
```

It should be noted that the plug-n-play controllers use `andheiberg/notify` to push success notifications to views. If you wish to use these controllers you will therefore need to install it. [Instructions can be found here](https://github.com/AndreasHeiberg/laravel-notify).

### Validation
Having a well build registration flow can save you from mountains of customer support and loss of income. For this reason UX is really important.

Registration validation is difficult to generalize, but login can easily be generalized.

- `UserNotFoundException` - We can't find a user with that e-mail address.

- `UserIncorrectPasswordException` - I think you mistyped your password.

- `UserUnverifiedException` - You haven't verified your email. <a href=\"/resend-verification-email\">Get a new verification email.</a>

- `UserDeactivatedException` - Your account has been disabled.

// Note registrating the same email will still throw db errors