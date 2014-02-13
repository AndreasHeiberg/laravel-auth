<?php namespace Andheiberg\Auth\Models;

use Andheiberg\Auth\UserInterface;
use Andheiberg\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Validation\Validator;
use \App;

class User extends Eloquent implements UserInterface, RemindableInterface {
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array('email', 'password', 'first_name', 'last_name');

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('*');

	/**
	 * Soft delete
	 *
	 * @var boolean
	 */
	protected $softDelete = true;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var Illuminate\Support\MessageBag
	 */
	public $errors;

	/**
	 * Validation rules.
	 *
	 * @var array
	 */
	protected $rules = [
		'email' => 'required|email|unique:users,email,:id:',
		'password' => 'required',
		'first_name' => 'required',
		'last_name' => 'required',
	];

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/**
	 * Validator instance
	 * 
	 * @var Illuminate\Validation\Validators
	 */
	protected $validator;

	public function __construct(array $attributes = array(), Validator $validator = null)
	{
		parent::__construct($attributes);

		$this->errors = new MessageBag;
		$this->validator = $validator ?: App::make('validator');
	}

	/**
	 * Listen for save event
	 */
	protected static function boot()
	{
		parent::boot();

		static::saving(function($model)
		{
			return $model->validate();
		});
	}

	/**
	 * Roles relationship
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function roles()
	{
		return $this->belongsToMany('Andheiberg\Verify\Models\Role', 'role_user')->withTimestamps();
	}

	/**
	 * Permissions relationship
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function permissions()
	{
		return $this->belongsToMany('Andheiberg\Verify\Models\Permission', 'permission_user')->withTimestamps();
	}

	/**
	 * Salts and saves the password
	 *
	 * @param string $password
	 */
	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = Hash::make($value);
	}

	/**
	 * Validate the model's attributes.
	 *
	 * @param  array  $rules
	 * @return bool
	 */
	public function validate(array $rules = array())
	{
		$rules = $this->processRules($rules ?: $this->rules);
		$validator = $this->validator->make($this->attributes, $rules);

		if ($validator->fails())
		{
			$this->errors = $validator->messages();
			return false;
		}

		return true;
	}

	/**
	 * Process validation rules.
	 *
	 * @param  array  $rules
	 * @return array  $rules
	 */
	protected function processRules(array $rules)
	{
		$id = $this->getKey();
		array_walk($rules, function(&$item) use ($id)
		{
			$item = stripos($item, ':id:') !== false ? str_ireplace(':id:', $id, $item) : $item;
		});

		return $rules;
	}

	/**
	 * Validate the model's attributes.
	 *
	 * @param  array  $rules
	 * @return bool
	 */
	public function activate()
	{
		$this->auth_activated = false;
		return $this->save();
	}

	/**
	 * Validate the model's attributes.
	 *
	 * @param  array  $rules
	 * @return bool
	 */
	public function deactivate()
	{
		$this->auth_deactivated = true;
		return $this->save();
	}

	public function isDeactivated()
	{
		return !! $this->auth_deactivated;
	}

	public function scopeActive($query)
	{
		return $query->where('auth_deactivated', false);
	}

	public function scopeDeactivated($query)
	{
		return $query->where('auth_deactivated', true);
	}







	public function addRole($role)
	{
		$id = is_int($role) ? (array) $role : Role::where('name', $role)->lists('id');

		return $this->roles()->sync($id);
	}

	/**
	 * Is the User a Role
	 *
	 * @param  array|string  $roles A single role or an array of roles
	 * @return boolean
	 */
	public function hasRole($roles)
	{
		$roles = is_array($roles) ?: array($roles);

		$valid = false;
		foreach ($this->roles as $role)
		{
			if (in_array($role->name, $roles))
			{
				$valid = true;
			}
		}

		return $valid;
	}

	/**
	 * Can the User do something
	 *
	 * @param  array|string $permissions Single permission or an array or permissions
	 * @return boolean
	 */
	public function can($permissions)
	{
		$permissions = is_array($permissions) ?: array($permissions);
		$roles = $this->roles;
		$roles_ids = [];

		// Are we a super admin?
		foreach ($roles as $role)
		{
			if ($role->name === Config::get('auth::admin'))
			{
				return true;
			}

			$roles_ids[] = $role->id;
		}

		$hasPermissons = Permission::join('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
		->whereIn('permission_role.role_id', $roles_ids)
		->get();

		foreach ($hasPermissons as $permission)
		{
			if (in_array($permission->name, $permissions))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Is the User a certain Level
	 *
	 * @param  integer $level
	 * @param  string $modifier [description]
	 * @return boolean
	 */
	public function hasLevel($level, $modifier = '>=')
	{
		$max = -1;
		$min = 100;
		$levels = array();

		foreach ($this->roles as $role)
		{
			$max = $role->level > $max
				? $role->level
				: $max;

			$min = $role->level < $min
				? $role->level
				: $min;

			$levels[] = $role->level;
		}

		switch ($modifier)
		{
			case '=':
				return in_array($level, $levels);
				break;

			case '>=':
				return $max >= $level;
				break;

			case '>':
				return $max > $level;
				break;

			case '<=':
				return $min <= $level;
				break;

			case '<':
				return $min < $level;
				break;

			default:
				return false;
				break;
		}
	}

	/**
	 * Remove a role from the user
	 *
	 * @param  array|string $roles Single role or an array or roles
	 * @return boolean
	 */
	public function revokeRole($roles)
	{
		$roles = is_array($roles) ?: array($roles);

		foreach ($roles as $role)
		{
			$this->roles()->whereName($role)->detach();
		}

		return $this;
	}

	/**
	 * Give a role to the user
	 *
	 * @param  array|string $roles Single role or an array or roles
	 * @return boolean
	 */
	public function assignRole($roles)
	{
		$roles = is_array($roles) ?: array($roles);

		foreach ($roles as $role)
		{
			$role = is_numeric($role) ? Role::find($role) : Role::whereName($role)->first();

			if ( ! $role )
			{
				throw new ModelNotFoundException();
			}

			$this->roles()->save($role);
		}
		
		return $this;
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