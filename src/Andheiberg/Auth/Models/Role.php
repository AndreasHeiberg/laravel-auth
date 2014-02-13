<?php namespace Andheiberg\Auth\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Andheiberg\Auth\Models\Permission;

class Role extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'roles';

	/**
	 * The attributes that can be set with Mass Assignment.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'description', 'level'];

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
	protected $rules = [];

	/**
     * Users relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function users()
	{
		return $this->belongsToMany('Andheiberg\Auth\Models\User')->withTimestamps();
	}

	/**
     * Permissions relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function permissions()
	{
		return $this->belongsToMany('Andheiberg\Auth\Models\Permission')->withTimestamps();
	}

	/**
	 * Add one or multiple permissons to a role
	 *
	 * @param  array|string $permissions Single permission or an array or permissions
	 * @return boolean
	 */
	public function addPermission($permissions)
	{
		$permissions = is_array($permissions) ?: array($permissions);

		// Are we a super admin?
		foreach ($permissions as $permission)
		{
			if ($p = Permission::where('name', $permission)->exists())
			{
				$this->permissions()->save($p);
			}
			else
			{
				$p = new Permission(['name' => $permission]);
				$this->permissions()->save($p);
			}
		}

		return $this;
	}

}