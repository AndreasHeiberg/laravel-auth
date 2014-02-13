<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permissions', function($table)
		{
			$table->increments('id');
			$table->string('name', 100)->index();
			$table->timestamps();
		});

		Schema::create('permission_role', function($table)
		{
			$table->integer('role_id')->unsigned()->index();
			$table->integer('permission_id')->unsigned()->index();
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
		Schema::drop('permissions');

		Schema::drop('permission_user');
	}

}