<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
           $table->string('immatriculation')->unique();
            $table->string('marque');
            $table->string('modele');
            $table->string('type_vehicule');
            $table->integer('kilometrage_actuel')->default(0);
            $table->enum('etat', ['disponible', 'en_entretien', 'hors_service']);

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
        Schema::dropIfExists('vehicles');
    }
}
