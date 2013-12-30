<?php namespace Andheiberg\Auth;

class UserNotFoundException extends \Exception {};
class UserUnverifiedException extends \Exception {};
class UserDeletedException extends \Exception {};
class UserPasswordIncorrectException extends \Exception {};
class UserValidationErrorsException extends \Exception {};
class InvalidTokenException extends \Exception {};
class InvalidPasswordException extends \Exception {};