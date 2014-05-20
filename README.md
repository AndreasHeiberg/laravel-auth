Auth
===
Extention of the illuminate/auth package for laravel.

The auth package is great, but it is perhaps a little to simplistic. I've tried to add the most essential functions to the package without bloading it.

This package builds upon the great foundation and adds:

- [Plug-n-Play](#plug-n-play) (views, routes, controllers, etc. is include so the most basic install takes 5 min.)
- [Email verification](#email-verification) (click a link in an email to verify your email)
- [Events for extendability](#events)
- [Move to exceptions for error handling](#exceptions)
- [More fine grained validation feedback (better UX)](#validation)
- [Included password hashing](#password-hashing) (less boilerplate code)


## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Plug-n-Play](#plug-n-play)
    - [Email Verification](#email-verification)
    - [Events](#events)
    - [Exceptions](#exceptions)
    - [Validation](#validation)
    - [Password Hashing](#password-hashing)

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

You then need to setup your db either with the supplied migration files or your own.

To use the packaged migrations run `php artisan migrate --bench="andheiberg/auth"`.

If you wish to write your own consider publishing the packaged stubs to your migrations folder with `php artisan auth:reminders-table` and `php artisan auth:users-table`.

## Configuration

Everything is configurable. The package can be change modularly (language files, views, controllers, routes, auth extension) at different levels.

**1 Level - Changing configuration options**

To create the configuration file run this command in your command line app:

```bash
$ php artisan config:publish andheiberg/auth
```

The configuration file will be published here: `app/config/packages/andheiberg/auth/config.php`.

Here you can change user model, table and a couple of other things as well as desable the package routing.

**2 Level - Change language files**

You might be fine with they layout set by the packaged views, but you wish to edit the text in them.

To create the language files run this command in your command line app:

```bash
$ php artisan auth:lang
```

The language file will be published here: `app/lang/packages/en/auth`.

**3 Level - Change views**

If you wish to change the layout of the auth pages or emails, you can overwrite the packaged views.

To create the new view files run this command in your command line app:

```bash
$ php artisan view:publish andheiberg/auth
```

The view files will be published here: `app/views/packages/andheiberg/auth`.

**4 Level - Change controllers and routes**

Login, Password Reset and Email Verification will most likely not need to be changed, but you might wish to add multiple steps to your signup flow at which point writing your own controller is needed.

To use the packaged controllers as a starting point run this command in your command line app:

```bash
$ php artisan auth:controllers
```

The controllers will be published here: `app/controllres/auth`.

In order to use these new controllers you will need to write new routes and disable the package routing in the config file.

**5 Level - Extend Auth**

As with illuminate/auth you can extend Auth with new users providers if you don't wish to use eloquent or would like to change hashing library or otherwise change the package.

[For more information](http://laravel.com/docs/extending#authentication)

## Usage

The original auth interface has not been changed, and for that reason, you can always use [the laravel docs for auth](http://laravel.com/docs/security).

I've added a `Auth::register()` command to make registration easier and more obvious.

It also allows me to hash passwords automatically and send verification emails. It also makes it possible to optionally log the user in after registration.

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

### Plug-n-Play
Authentication is needed on most projects. Copy-pasting or rewriting every related view, controller, localization file and route is time consuming and wasteful.

Auth will work out of the box. The package has everything you need to get started, yet everything is custimizable. You can disable the routes and write your own controllers, views, routes, etc.

The routes can be disabled in the config file.

You can then create your own controllers from scratch or use ours as a starting point.

To move our controllers to your `app/controllers` use `php artisan auth:controllers`.

To move our views to your `app/views` use `php artisan view:publish andheiberg/auth`.

To move our localization files to your `app/lang` use `php artisan auth:lang`.

It should be noted that the plug-n-play controllers use `andheiberg/notify` to push success notifications to views. If you wish to use these controllers you will therefore need to install it. [Instructions can be found here](https://github.com/AndreasHeiberg/laravel-notify).

### Email verification
Fake or incorrectly typed emails are a problem if you use transactional emails. Additionally you will mostlikly want to create a barrier for spammers, so they can't easily create infinite accounts.

The most common way of solving this is by sending an verification email to new account holders after they signup. The email contains a unique link they need to click to verify their email address.

This has been added to the signup flow.

You can overwrite the email view in `app/views/packages/auth/emails/verification.blade.php` (see [Plug-n-Play](#plug-n-play))

`AuthVerification::remind()` and `AuthVerification::verify()` can be used to resend the email and verify an email with the sent token.

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
- `auth.logout`
- `auth.resetting-password`
- `auth.password-reset`


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

### Validation
Having a well build registration flow can save you from mountains of customer support and loss of income. For this reason UX is really important.

Registration validation is difficult to generalize, but login can easily be generalized.

- `UserNotFoundException` - We can't find a user with that e-mail address.

- `UserIncorrectPasswordException` - I think you mistyped your password.

- `UserUnverifiedException` - You haven't verified your email. <a href=\"/resend-verification-email\">Get a new verification email.</a>

- `UserDeactivatedException` - Your account has been disabled.
