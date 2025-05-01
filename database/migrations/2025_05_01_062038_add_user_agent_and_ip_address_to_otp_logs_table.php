<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAgentAndIpAddressToOtpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otp_logs', function (Blueprint $table) {
            $table->string('user_agent')->nullable(); // Add user_agent column
            $table->string('ip_address')->nullable(); // Add ip_address column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('otp_logs', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'ip_address']);
        });
    }
}
