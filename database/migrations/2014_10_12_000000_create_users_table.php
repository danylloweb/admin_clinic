<?php

use App\Entities\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique()->index();
            $table->string('cpf', 11)->unique()->index();
            $table->string('password');
            $table->string('phone', 15)->unique()->index();
            $table->integer('user_type_id')->unsigned()->default(1);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('user_type_id')->references('id')->on('user_types');
        });

        User::create([
            'name'         => 'Danyllo Ferreira Santos da Silva',
            'email'        => 'danyllo@impactadigital.net',
            'cpf'          => '08612634482',
            'password'     => bcrypt('elo1234*'),
            'phone'        => '81985879004',
            'birthdate'    => '1990-06-27',
            'user_type_id' => 3,
        ]);

        User::create([
            'name'         => 'Erika Ferreira de Moraes',
            'email'        => 'rk.fm89@hotmail.com',
            'cpf'          => '08430312471',
            'password'     => bcrypt('24063004'),
            'phone'        => '81973267896',
            'birthdate'    => '1989-04-30',
            'user_type_id' => 3,
        ]);

        User::create([
            'name'         => 'Priscila Ruanda Felix da Silva',
            'email'        => 'gerencia@impactadigital.net',
            'cpf'          => '37017361032',
            'password'     => bcrypt('0048*'),
            'phone'        => '81983256281',
            'birthdate'    => '1994-10-11',
            'user_type_id' => 3,
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
