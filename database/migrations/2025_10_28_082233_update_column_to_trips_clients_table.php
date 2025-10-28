<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnToTripsClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_clients', function (Blueprint $table) {
            $table->string("notes_livraison")->nullable();
            $table->integer('ordre_visite')->default(1); // Ordre de visite dans le trajet

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_clients', function (Blueprint $table) {
            $table->dropColumn("notes_livraison");
            $table->dropColumn('ordre_visite'); // Ordre de visite dans le trajet

        });
    }
}
