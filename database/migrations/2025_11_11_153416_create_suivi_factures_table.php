<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuiviFacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivi_factures', function (Blueprint $table) {
            $table->id();
             $table->string('numero_facture')->unique();
            $table->date('date_livraison');
            $table->decimal('montant', 10, 2);
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Index pour les performances
            $table->index(['client_id', 'date_livraison']);
            $table->index('numero_facture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suivi_factures');
    }
}
