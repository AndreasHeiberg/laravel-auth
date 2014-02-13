<?php namespace Andheiberg\Auth\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Permission extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'permissions';

	/**
	 * The attributes that can be set with Mass Assignment.
	 *
	 * @var array
	 */
	protected $fillable = ['name'];

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
     * Roles relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function roles()
	{
		return $this->hasMany('Andheiberg\Auth\Models\Role')->withTimestamps();
	}

}