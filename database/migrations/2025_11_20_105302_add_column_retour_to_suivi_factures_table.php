<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRetourToSuiviFacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suivi_factures', function (Blueprint $table) {
            $table->decimal('montant_retour', 10, 2)->default(0)->after('montant');
            $table->text('raison_retour')->nullable()->after('montant_retour');
            $table->date('date_retour')->nullable()->after('raison_retour');
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
            $table->dropColumn(['montant_retour', 'raison_retour', 'date_retour']);
        });
    }
}
