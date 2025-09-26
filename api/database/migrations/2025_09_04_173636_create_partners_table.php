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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->index();
            $table->string('hash')->nullable()->index();
            $table->string('login_email')->nullable()->index();
            $table->string('password')->nullable()->index();
            $table->string('store_name')->nullable()->index();
            $table->string('company_name')->nullable()->index();
            $table->string('postal_code')->nullable()->nullable();
            $table->string('prefecture')->nullable()->nullable();
            $table->string('city')->nullable()->nullable();
            $table->string('address')->nullable()->nullable();
            $table->string('representative_familyname')->nullable();
            $table->string('representative_firstname')->nullable();
            $table->string('representative_familyname_kana')->nullable();
            $table->string('representative_firstname_kana')->nullable();
            $table->string('representative_position')->nullable();
            $table->string('skill')->nullable();
            $table->string('area')->nullable();
            $table->string('business_hours')->nullable();
            $table->string('fax')->nullable();
            $table->string('url')->nullable();
            $table->float('share')->nullable();
            $table->text('transfer_account')->nullable();
            $table->integer('transfer_fee')->nullable()->default(0);
            $table->integer('share_method_id')->nullable()->default(0);
            $table->integer('parent_company_id')->nullable()->default(0);
            $table->time('business_hour_from')->nullable();
            $table->time('business_hour_to')->nullable();
            $table->string('holidays')->nullable();
            $table->decimal('latitude',10,7)->nullable();
            $table->decimal('longitude',10,7)->nullable();
            $table->integer('score')->nullable()->default(0);
            $table->text('react_scopes')->nullable();
            $table->string('ext1')->nullable();
            $table->string('ext2')->nullable();
            $table->string('ext3')->nullable();
            $table->integer('contract_company_id')->nullable()->default(0);
            $table->text('payer_name')->nullable();
            $table->text('exclude_payer_name')->nullable();
            $table->boolean('show_schedule')->nullable()->default(false);
            $table->boolean('check_daily_sales')->nullable()->default(false);
            $table->boolean('check_daily_payment')->nullable()->default(false);
            $table->string('chatwork_id')->nullable();
            $table->integer('last_month_opportunities')->nullable()->default(0);
            $table->integer('last_month_profit_average')->nullable()->default(0);
            $table->integer('last_month_performance_id')->nullable()->default(0);
            $table->string('shoukai')->nullable();
            $table->integer('kaitori')->nullable()->default(0);
            $table->integer('senzoku')->nullable()->default(0);
            $table->integer('p_sortid')->nullable()->default(0);
            $table->string('schedule_color')->nullable();
            $table->datetime('lastLogin')->nullable();
            $table->boolean('is_ars_employee')->nullable()->default(false);
            $table->boolean('is_ac_designated')->nullable()->default(false);
            $table->boolean('is_insect_designated')->nullable()->default(false);
            $table->boolean('is_hojin_designated')->nullable()->default(false);
            $table->boolean('is_capital_area_designated')->nullable()->default(false);
            $table->boolean('is_suburbs_designated')->nullable()->default(false);
            $table->boolean('is_only_aircon')->nullable()->default(false);
            $table->integer('hojin_p_sortid')->nullable()->nullable();
            $table->text('memo')->nullable();
            $table->text('note')->nullable();
            $table->boolean('disabled')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
