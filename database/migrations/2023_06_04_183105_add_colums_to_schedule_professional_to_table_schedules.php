<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumsToScheduleProfessionalToTableSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('has_medical',['Sim','Não'])->default('Não')->after('img');
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->integer('professional_id')->nullable()->after('observation_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_professional_to_table_schedules', function (Blueprint $table) {
            //
        });
    }
}
