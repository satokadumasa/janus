<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('group_id')->after('id')->nullable();
            $table->string('username')->after('group_id')->nullable();
            $table->string('phone')->after('remember_token')->nullable();
            $table->string('familyname')->after('phone')->nullable();
            $table->string('firstname')->after('familyname')->nullable();
            $table->string('chatwork_id')->after('firstname')->nullable();
            $table->string('loginext')->after('chatwork_id')->nullable();
            $table->tinyInteger('is_displayed_achievement')->after('loginext')->nullable();
            $table->tinyInteger('attendance_managed')->after('is_displayed_achievement')->nullable();
            $table->dropColumn('name');
            
            $table->index(['email']);
			$table->index(['username']);
			$table->index(['familyname']);
			$table->index(['firstname']);
			$table->index(['group_id']);
			$table->index(['attendance_managed']);
			$table->index(['chatwork_id']);
			$table->index(['loginext']);
			$table->index(['is_displayed_achievement']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->dropColumn('username');
            $table->dropColumn('familyname');
            $table->dropColumn('firstname');
            $table->dropColumn('group_id');
            $table->dropColumn('attendance_managed');
            $table->dropColumn('chatwork_id');
            $table->dropColumn('loginext');
            $table->dropColumn('NewPassword');
            $table->dropColumn('is_displayed_achievement');
        });
    }
};
