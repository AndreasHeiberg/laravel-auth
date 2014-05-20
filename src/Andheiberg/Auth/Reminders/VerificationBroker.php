<?php namespace Andheiberg\Auth\Reminders;

use Closure;
use Illuminate\Mail\Mailer;
use Andheiberg\Auth\UserProviderInterface;

class VerificationBroker {

	/**
	 * Constant representing a successfully sent reminder.
	 *
	 * @var int
	 */
	const REMINDER_SENT = 'reminders.sent';

	/**
	 * Constant representing a successfully verification.
	 *
	 * @var int
	 */
	const EMAIL_VERIFIED = 'reminders.verified';

	/**
	 * Constant representing the user not found response.
	 *
	 * @var int
	 */
	const INVALID_USER = 'reminders.user';

	/**
	 * Constant representing an invalid token.
	 *
	 * @var int
	 */
	const INVALID_TOKEN = 'reminders.token';

	/**
	 * The verification reminder repository.
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
	 * @var \Illuminate\Mail\Mailer
	 */
	protected $mailer;

	/**
	 * The view of the verification reminder e-mail.
	 *
	 * @var string
	 */
	protected $reminderView;

	/**
	 * Create a new verification broker instance.
	 *
	 * @param  \Andheiberg\Auth\Reminders\ReminderRepositoryInterface  $reminders
	 * @param  \Andheiberg\Auth\UserProviderInterface  $users
	 * @param  \Illuminate\Mail\Mailer  $mailer
	 * @param  string  $reminderView
	 * @return void
	 */
	public function __construct(ReminderRepositoryInterface $reminders,
                                UserProviderInterface $users,
                                Mailer $mailer,
                                $reminderView)
	{
		$this->users = $users;
		$this->mailer = $mailer;
		$this->reminders = $reminders;
		$this->reminderView = $reminderView;
	}

	/**
	 * Send a verification reminder to a user.
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
			return self::INVALID_USER;
		}

		// Once we have the reminder token, we are ready to send a message out to the
		// user with a link to verify their email. We will then redirect back to
		// the current URI having nothing set in the session to indicate errors.
		$token = $this->reminders->create($user);

		$this->sendReminder($user, $token, $callback);

		return self::REMINDER_SENT;
	}

	/**
	 * Send the verification reminder e-mail.
	 *
	 * @param  \Andheiberg\Auth\Reminders\RemindableInterface  $user
	 * @param  string   $token
	 * @param  Closure  $callback
	 * @return int
	 */
	public function sendReminder(RemindableInterface $user, $token, Closure $callback = null)
	{
		// We will use the reminder view that was given to the broker to display the
		// verification reminder e-mail. We'll pass a "token" variable into the views
		// so that it may be displayed for an user to click for email verification.
		$view = $this->reminderView;

		return $this->mailer->send($view, compact('token', 'user'), function($message) use ($user, $token, $callback)
		{
			$message->subject('Please verify your email');
			$message->to($user->getReminderEmail());

			if ( ! is_null($callback)) call_user_func($callback, $message, $user, $token);
		});
	}

	/**
	 * Verify the email for the given token.
	 *
	 * @param  array    $credentials
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function verify(array $credentials)
	{
		// If the responses from the validate method is not a user instance, we will
		// assume that it is a redirect and simply return it from this method and
		// the user is properly redirected having an error message on the post.
		$user = $this->validateVerification($credentials);

		if ( ! $user instanceof RemindableInterface)
		{
			return $user;
		}

		$this->users->updateAuthVerified($user, true);

		$this->reminders->delete($credentials['token']);

		return self::EMAIL_VERIFIED;
	}

	/**
	 * Validate a email verification for the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Andheiberg\Auth\Reminders\RemindableInterface
	 */
	protected function validateVerification(array $credentials)
	{
		if (is_null($user = $this->getUser($credentials)))
		{
			return self::INVALID_USER;
		}

		if ( ! $this->reminders->exists($user, $credentials['token']))
		{
			return self::INVALID_TOKEN;
		}

		return $user;
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
	 * Get the verification reminder repository implementation.
	 *
	 * @return \Andheiberg\Auth\Reminders\ReminderRepositoryInterface
	 */
	protected function getRepository()
	{
		return $this->reminders;
	}

}
