<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPrlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_lines', function (Blueprint $table) {
            $table->string('next_assembly')->nullable()->after('po_number');
            $table->string('work_order')->nullable()->after('next_assembly');
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
            $table->dropColumn('next_assembly');
            $table->dropColumn('work_order');
        });
    }
}
