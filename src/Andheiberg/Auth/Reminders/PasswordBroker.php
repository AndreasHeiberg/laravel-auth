<?php namespace Andheiberg\Auth\Reminders;

use Closure;
use Illuminate\Mail\Mailer;
use Andheiberg\Auth\UserProviderInterface;
use Illuminate\Support\MessageBag;

class PasswordBroker {

	/**
	 * The password reminder repository.
	 *
	 * @var \Andheiberg\Auth\Reminders\ReminderRepositoryInterface  $reminders
	 */
	protected $reminders;

	/**
	 * The user provider implementation.
	 *
	 * @var \Andheiberg\Auth\UserProviderInterface
	 */
	protected $users;

	/**
	 * The mailer instance.
	 *
	 * @var \Andheiberg\Mail\Mailer
	 */
	protected $mailer;

	/**
	 * The view of the password reminder e-mail.
	 *
	 * @var string
	 */
	protected $reminderView;

	/**
	 * The subject of the email verification reminder e-mail.
	 *
	 * @var string
	 */
	protected $reminderSubject;

	/**
	 * The custom password validator callback.
	 *
	 * @var \Closure
	 */
	protected $passwordValidator;

	/**
	 * Validation erros
	 *
	 * @var Andheiberg\Support\MessageBag
	 */
	public $errors;

	/**
	 * Create a new password broker instance.
	 *
	 * @param  \Andheiberg\Auth\Reminders\ReminderRepositoryInterface  $reminders
	 * @param  \Andheiberg\Auth\UserProviderInterface  $users
	 * @param  \Andheiberg\Mail\Mailer  $mailer
	 * @param  string  $reminderView
	 * @param  string  $reminderSubject
	 * @return void
	 */
	public function __construct(ReminderRepositoryInterface $reminders,
                                UserProviderInterface $users,
                                Mailer $mailer,
                                MessageBag $errors,
                                $reminderView,
                                $reminderSubject)
	{
		$this->reminders = $reminders;
		$this->users = $users;
		$this->mailer = $mailer;
		$this->errors = $errors;
		$this->reminderView = $reminderView;
		$this->reminderSubject = $reminderSubject;
	}

	/**
	 * Send a password reminder to a user.
	 *
	 * @param  array    $credentials
	 * @param  Closure  $callback
	 * @return string
	 */
	public function remind(array $credentials, Closure $callback = null)
	{
		// First we will check to see if we found a user at the given credentials and
		// if we did not we will redirect back to this current URI with a piece of
		// "flash" data in the session to indicate to the developers the errors.
		$user = $this->getUser($credentials);

		if (is_null($user))
		{
			$this->errors->add('email', 'User not found');
			return $this;
		}

		// Once we have the reminder token, we are ready to send a message out to the
		// user with a link to reset their password. We will then redirect back to
		// the current URI having nothing set in the session to indicate errors.
		$token = $this->reminders->create($user);

		$this->sendReminder($user, $token, $callback);

		return $this;
	}

	/**
	 * Send the password reminder e-mail.
	 *
	 * @param  \Andheiberg\Auth\Reminders\RemindableInterface  $user
	 * @param  string   $token
	 * @param  Closure  $callback
	 * @return void
	 */
	public function sendReminder(RemindableInterface $user, $token, Closure $callback = null)
	{
		// We will use the reminder view that was given to the broker to display the
		// password reminder e-mail. We'll pass a "token" variable into the views
		// so that it may be displayed for an user to click for password reset.
		$view = $this->reminderView;
		$subject = $this->reminderSubject;

		return $this->mailer->send($view, compact('token', 'user'), function($m) use ($user, $subject, $callback)
		{
			$m->to($user->getReminderEmail());
			$m->subject($subject);

			if ( ! is_null($callback)) call_user_func($callback, $m, $user);
		});
	}

	/**
	 * Reset the password for the given token.
	 *
	 * @param  array    $credentials
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function reset(array $credentials, Closure $callback = null)
	{
		// If the responses from the validate method is not a user instance, we will
		// assume that it is a redirect and simply return it from this method and
		// the user is properly redirected having an error message on the post.
		try {
			$user = $this->validateReset($credentials);
		}
		catch (Andheiberg\Auth\UserNotFoundException $e) {
			$this->errors->add('email', 'User not found.');
		}
		catch (Andheiberg\Auth\InvalidPasswordException $e) {
			$this->errors->add('password', 'Invalid password.');
		}
		catch (Andheiberg\Auth\InvalidTokenException $e) {
			$this->errors->add('email', 'Invalid token.');
		}

		if ( ! $user instanceof RemindableInterface)
		{
			return $this;
		}

		$password = $credentials['password'];

		// Update the users password
		$user->password = $password;
		$user->save();

		// Once we have called this callback, we will remove this token row from the
		// table and return the response from this callback so the user gets sent
		// to the destination given by the developers from the callback return.
		if ( ! is_null($callback)) call_user_func($callback, $user, $password);

		$this->reminders->delete($credentials['token']);

		return $this;
	}

	/**
	 * Validate a password reset for the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Andheiberg\Auth\RemindableInterface
	 */
	protected function validateReset(array $credentials)
	{
		if (is_null($user = $this->getUser($credentials)))
		{
			throw new Andheiberg\Auth\UserNotFoundException;
		}

		if ( ! $this->validNewPasswords($credentials))
		{
			throw new Andheiberg\Auth\InvalidPasswordException;
		}

		if ( ! $this->reminders->exists($user, $credentials['token']))
		{
			throw new Andheiberg\Auth\InvalidToken;
		}

		return $user;
	}

	/**
	 * Set a custom password validator.
	 *
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function validator(Closure $callback)
	{
		$this->passwordValidator = $callback;
	}

	/**
	 * Determine if the passwords match for the request.
	 *
	 * @param  array  $credentials
	 * @return bool
	 */
	protected function validNewPasswords(array $credentials)
	{
		if (isset($this->passwordValidator))
		{
			if (isset($credentials['password_confirmation']))
			{
				return call_user_func($this->passwordValidator, $credentials) and $credentials['password'] == $credentials['password_confirmation'];
			}

			return call_user_func($this->passwordValidator, $credentials);
		}
		else
		{
			return $this->validatePasswordWithDefaults($credentials);
		}
	}

	/**
	 * Determine if the passwords are valid for the request.
	 *
	 * @param  array  $credentials
	 * @return bool
	 */
	protected function validatePasswordWithDefaults(array $credentials)
	{
		if (isset($credentials['password_confirmation']))
		{
			$matches = $credentials['password'] == $credentials['password_confirmation'];

			return $matches && $credentials['password'] && strlen($credentials['password']) >= 6;
		}	

		return $credentials['password'] && strlen($credentials['password']) >= 6;
	}

	/**
	 * Get the user for the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Andheiberg\Auth\Reminders\RemindableInterface
	 *
	 * @throws \UnexpectedValueException
	 */
	public function getUser(array $credentials)
	{
		$credentials = array_except($credentials, array('token'));

		$user = $this->users->retrieveByCredentials($credentials);

		if ($user && ! $user instanceof RemindableInterface)
		{
			throw new \UnexpectedValueException("User must implement Remindable interface.");
		}

		return $user;
	}

	/**
	 * Get the password reminder repository implementation.
	 *
	 * @return \Andheiberg\Auth\Reminders\ReminderRepositoryInterface
	 */
	protected function getRepository()
	{
		return $this->reminders;
	}

	/**
	 * Determine if the user was authenticated via "remember me" cookie.
	 *
	 * @return bool
	 */
	public function hasErrors()
	{
		return ! $this->errors->isEmpty();
	}

}