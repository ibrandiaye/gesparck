<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_entries', function (Blueprint $table) {
             $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('date_remplissage');
            $table->decimal('prix_litre', 8, 3); // Prix du litre en CFA
            $table->decimal('litres', 8, 2); // Quantité en litres
            $table->decimal('cout_total', 10, 2); // Coût total en CFA
            $table->integer('kilometrage'); // Kilométrage au moment du remplissage
            $table->string('station')->nullable(); // Nom de la station
            $table->string('type_carburant')->default('diesel'); // diesel, essence, SP95, etc.
            $table->text('notes')->nullable();
            $table->string('preuve')->nullable(); // Photo du ticket ou facture

            // Index pour les performances
            $table->index(['vehicle_id', 'date_remplissage']);
            $table->index('type_carburant');
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
        Schema::dropIfExists('fuel_entries');
    }
}
