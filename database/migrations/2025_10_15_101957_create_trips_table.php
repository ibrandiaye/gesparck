<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('fuel_entry_id')->constrained()->onDelete('cascade');
        // $table->foreignId('conducteur_id')->constrained('users')->onDelete('cascade');
            $table->string('destination');
            $table->enum('motif', ['livraison', 'client', 'maintenance', 'administratif', 'autre']);
            $table->integer('nombre_trajets')->default(1); // Nombre d'allers-retours
            $table->date('date_trajet');
        //  $table->integer('km_depart');
        //  $table->integer('km_arrivee');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('trips');
    }
}
