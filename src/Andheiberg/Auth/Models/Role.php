<?php namespace Andheiberg\Auth\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

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
     * Users
     *
     * @return object
     */
	public function users()
	{
		return $this->belongsToMany('Models\User')->withTimestamps();
	}

}