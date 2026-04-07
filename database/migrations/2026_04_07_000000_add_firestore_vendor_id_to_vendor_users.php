<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirestoreVendorIdToVendorUsers extends Migration
{
    public function up()
    {
        Schema::table('vendor_users', function (Blueprint $table) {
            $table->string('firestore_vendor_id')->nullable()->after('uuid');
        });
    }

    public function down()
    {
        Schema::table('vendor_users', function (Blueprint $table) {
            $table->dropColumn('firestore_vendor_id');
        });
    }
}
