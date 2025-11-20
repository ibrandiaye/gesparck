<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
             $table->foreignId('suivi_facture_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement');
            $table->string('mode_paiement')->default('virement'); // virement, chèque, espèce, carte
            $table->string('reference')->nullable(); // référence du paiement
            $table->text('notes')->nullable();
            $table->enum('statut', ['complet', 'partiel', 'en_attente'])->default('complet');
            $table->timestamps();

             // Index pour les performances
            $table->index(['suivi_facture_id', 'date_paiement']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}
