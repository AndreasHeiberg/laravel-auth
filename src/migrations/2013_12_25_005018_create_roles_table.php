<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function($table)
		{
			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->integer('level');
			$table->timestamps();
		});

		Schema::create('role_user', function($table)
		{
			$table->integer('user_id')->unsigned()->index();
			$table->integer('role_id')->unsigned()->index();
			$table->timestamps();

			// $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			// $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles');

		Schema::drop('role_user');
	}

}