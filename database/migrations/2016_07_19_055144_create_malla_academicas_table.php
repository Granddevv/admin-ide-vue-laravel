<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMallaAcademicasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('malla_academica', function(Blueprint $table) {
            $table->increments('id');

			$table->string('codigo_materia')->nullable(false);
			$table->string('nombre_materia')->nullable(false);
			$table->string('semestre')->nullable(false);
			$table->integer('horas')->nullable(false);
			$table->enum('tipo_materia', ['BASICA', 'PROFESIONAL', 'GENERAL', 'OPTATIVA']);
			$table->enum('tipo_asignacion', ['NORMAL', 'ESPECIAL']);
			$table->enum('estado', ['ACTIVO', 'INACTIVO'])->nullable(false);

            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('malla_academica');
	}

}
