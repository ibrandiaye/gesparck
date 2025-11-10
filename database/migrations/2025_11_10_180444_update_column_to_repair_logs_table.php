<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnToRepairLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_logs', function (Blueprint $table) {
            $table->string('type_intervention', )->default('entretien_routine')->change();
                    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repair_logs', function (Blueprint $table) {
            $table->enum('type_intervention', [
                'entretien_routine',
                'reparation',
                'vidange',
                'freinage',
                'pneumatique',
                'electrique',
                'mecanique',
                'carrosserie',
                'autre'
            ])->default('entretien_routine');
        });
    }
}
