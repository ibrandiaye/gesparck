<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEtatToSuiviFacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suivi_factures', function (Blueprint $table) {
            $table->date('date_livraison')->nullable()->change();
            $table->enum('etat', ['livré', 'non livré'])->default('non livré'); // Nouveau champ

            $table->index('etat');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suivi_factures', function (Blueprint $table) {
            $table->date('date_livraison');
        });
    }
}
