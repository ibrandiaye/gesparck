<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('repair_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('date_intervention');
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
            $table->string('description');
            $table->text('details_travaux')->nullable();
            $table->decimal('cout_main_oeuvre', 10, 2)->default(0);
            $table->decimal('cout_pieces', 10, 2)->default(0);
            $table->decimal('cout_total', 10, 2)->default(0);
            $table->integer('kilometrage_vehicule');
            $table->string('garage')->nullable();
            $table->string('technicien')->nullable();
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->date('date_prochaine_revision')->nullable();
            $table->integer('prochain_kilometrage')->nullable();
            $table->text('notes')->nullable();
            $table->string('facture')->nullable(); // PDF ou image de la facture
            $table->timestamps();

            // Index pour les performances
            $table->index(['vehicle_id', 'date_intervention']);
            $table->index('type_intervention');
            $table->index('statut');
        });
    }

    public function down()
    {
        Schema::dropIfExists('repair_logs');
    }
};
