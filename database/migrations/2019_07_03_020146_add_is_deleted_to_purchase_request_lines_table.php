<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDeletedToPurchaseRequestLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_lines', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(0)->after('status');
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
            $table->dropColumn('is_deleted');
        });
    }
}
