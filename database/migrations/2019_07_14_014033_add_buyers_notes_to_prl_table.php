<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuyersNotesToPrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_lines', function (Blueprint $table) {
            $table->longText('buyers_notes')->nullable()->after('work_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_request_lines', function (Blueprint $table) {
            $table->dropColumn('buyers_notes');
        });
    }
}
