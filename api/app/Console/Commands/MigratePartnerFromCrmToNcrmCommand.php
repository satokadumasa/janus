<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\API\GMO\V1\user AS user_model;
use App\Models\API\GMO\V1\account;
use App\Models\API\GMO\V1\opportunity AS oppo;
use App\Models\User;
use App\Models\Partner;
use App\Models\EmailAddress;
use App\Models\PartnerEmailAddress;
use App\Models\Opportunity;

class MigratePartnerFromCrmToNcrmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:migrate_from_crm_to_ncrm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->convPartner();
        $this->convUsers();
        $this->convOpportunitis();
    }

    public function convPartner()
    {
        echo "convPartner() start\n";
        // DB::beginTransaction();
        $accounts = account::orderBy('id', 'asc')->get();
        echo "accounts count[" . count($accounts) . "]\n";
        $partner_id = 0;
        foreach($accounts AS $account) {
            echo "account_id[{$account->id}]\n";

            $partner = Partner::create([
                'id' => $account['id'],
                'user_id' => $account['user_id'],
                'hash' => $account['hash'],
                'login_email' => $account['login_email'],
                'password' => $account['password'],
                'store_name' => $account['store_name'],
                'company_name' => $account['company_name'],
                'postal_code' => $account['postal_code'],
                'prefecture' => $account['prefecture'],
                'city' => $account['city'],
                'address' => $account['address'],
                'representative_familyname' => $account['representative_familyname'],
                'representative_firstname' => $account['representative_firstname'],
                'representative_familyname_kana' => $account['representative_familyname_kana'],
                'representative_firstname_kana' => $account['representative_firstname_kana'],
                'representative_position' => $account['representative_position'],
                'skill' => $account['skill'],
                'area' => $account['area'],
                'business_hours' => $account['business_hours'],
                'fax' => $account['fax'],
                'url' => $account['url'],
                'share' => $account['share'],
                'transfer_account' => $account['transfer_account'],
                'transfer_fee' => $account['transfer_fee'],
                'share_method_id' => $account['share_method_id'],
                'parent_company_id' => $account['parent_company_id'],
                'business_hour_from' => $account['business_hour_from'],
                'business_hour_to' => $account['business_hour_to'],
                'holidays' => $account['holidays'],
                'latitude' => $account['latitude'],
                'longitude' => $account['longitude'],
                'score' => $account['score'],
                'react_scopes' => $account['react_scopes'],
                'ext1' => $account['ext1'],
                'ext2' => $account['ext2'],
                'ext3' => $account['ext3'],
                'contract_company_id' => $account['contract_company_id'],
                'payer_name' => $account['payer_name'],
                'exclude_payer_name' => $account['exclude_payer_name'],
                'show_schedule' => $account['show_schedule'],
                'check_daily_sales' => $account['check_daily_sales'],
                'check_daily_payment' => $account['check_daily_payment'],
                'chatwork_id' => $account['chatwork_id'],
                'last_month_opportunities' => $account['last_month_opportunities'],
                'last_month_profit_average' => $account['last_month_profit_average'],
                'last_month_performance_id' => $account['last_month_performance_id'],
                'shoukai' => $account['shoukai'],
                'kaitori' => $account['kaitori'],
                'senzoku' => $account['senzoku'],
                'p_sortid' => $account['p_sortid'],
                'schedule_color' => $account['schedule_color'],
                'lastLogin' => $account['lastLogin'],
                'is_ars_employee' => $account['is_ars_employee'],
                'is_ac_designated' => $account['is_ac_designated'],
                'is_insect_designated' => $account['is_insect_designated'],
                'is_hojin_designated' => $account['is_hojin_designated'],
                'is_capital_area_designated' => $account['is_capital_area_designated'],
                'is_suburbs_designated' => $account['is_suburbs_designated'],
                'is_only_aircon' => $account['is_only_aircon'],
                'hojin_p_sortid' => $account['hojin_p_sortid'],
                'memo' => $account['memo'],
                'note' => $account['note'],
                'disabled' => $account['disabled'],
            ]);
            
            $partner_id = $partner->id;

            for($i=1 ; $i < 4; $i++){
                $col_neme = "email{$i}";
                $col_neme = "opportunity_email{$i}";
                if($account->$col_neme) {
                    $eaddress = EmailAddress::create([
                        'type_id' => 1,
                        'meiladdress' => $account->$col_neme,
                    ]);
                    $pea = PartnerEmailAddress::create([
                        'partner_id' => $partner_id,
                        'email_address_id' => $eaddress->id,
                    ]);
                }

                if($account->$col_neme) {
                    $eaddress = EmailAddress::create([
                        'type_id' => 2,
                        'meiladdress' => $account->$col_neme,
                    ]);

                    $pea = PartnerEmailAddress::create([
                        'partner_id' => $partner_id,
                        'email_address_id' => $eaddress->id,
                    ]);
                }

                $col_neme = "billing_email{$i}";
                if($account->$col_neme) {
                    $eaddress = EmailAddress::create([
                        'type_id' => 3,
                        'meiladdress' => $account->$col_neme,
                    ]);
                    $pea = PartnerEmailAddress::create([
                        'partner_id' => $partner_id,
                        'email_address_id' => $eaddress->id,
                    ]);
                }
                
                // $col_neme = "phone{$i}";
                // echo"phone{$i}[{$account->$col_neme}]\n";
            }
        }
        $partner_id++;
        DB::statement("ALTER TABLE partners AUTO_INCREMENT = {$partner_id};");
        // DB::commit();
        echo "convPartner() end\n";
    }

    public function convUsers()
    {
        echo "convUsers() start\n";
        $user_models = user_model::orderBy('id', 'asc')->get();
        $user_id = 0;
        // DB::beginTransaction();
        foreach ($user_models as $key => $user_model) {
            echo "convUsers() user_model->id[{$user_model->id}]\n";
            $user = User::create([
                'id'                         => $user_model->id,
                'username'                   => $user_model->username,
                'password'                   => $user_model->NewPassword ? $user_model->NewPassword : '',
                'familyname'                 => $user_model->familyname,
                'firstname'                  => $user_model->firstname,
                'phone'                      => $user_model->phone,
                'email'                      => $user_model->email ? $user_model->email : '',
                'group_id'                   => $user_model->group_id,
                'attendance_managed'         => $user_model->attendance_managed,
                'chatwork_id'                => $user_model->chatwork_id,
                'created'                    => $user_model->created,
                'modified'                   => $user_model->modified,
                'loginext'                   => $user_model->loginext,
                'is_displayed_achievemen'    => $user_model->is_displayed_achievemen,
            ]);
            $user_id = $user_model->id;
            echo "user_id[{$user_id}]\n";
        }
        $user_id++;
        echo "user_id(2)[{$user_id}]\n";
        DB::statement("ALTER TABLE users AUTO_INCREMENT = {$user_id};");
        // DB::commit();
        echo "convUsers() end\n";
    }
    public function convOpportunitis()
    {
        echo "convOpportunitis() start\n";
        ini_set('memory_limit', '8192M');
        $start = 1;
        $end = 2000;
        $opportunity = null;
        $opportunity_id = 0;
        while ($end <= 1700000) {
            // $oppos = oppo::whereBetween('id', [$start, $end])->orderBy('id', 'asc')->where('id', '>', 1023391)->get();
            $oppos = oppo::whereBetween('id', [$start, $end])->orderBy('id', 'asc')->get();
            foreach ($oppos as $key => $oppo) {
                echo "oppo->id[{$oppo->id}]\n";
                $opportunity = Opportunity::create([
                    'id'                            => $oppo->id,
                    'hash'                          => $oppo->hash,
                    'field_id'                      => $oppo->field_id,
                    'dial_id'                       => $oppo->dial_id,
                    'firstname'                     => $oppo->firstname,
                    'familyname'                    => $oppo->familynam,
                    'firstname_kana'                => $oppo->firstname_kana,
                    'familyname_kana'               => $oppo->familyname_kana,
                    'gender_id'                     => $oppo->gender_id,
                    'generation_id'                 => $oppo->generation_id,
                    'mail_address'                  => $oppo->mail_address,
                    'report_mail'                   => $oppo->report_mail,
                    'estimation_email_address'      => $oppo->estimation_email_address,
                    'photo_report_email_address'    => $oppo->photo_report_email_address,
                    'company_name'                  => $oppo->company_name,
                    'prefecture'                    => $oppo->prefecture,
                    'city'                          => $oppo->city,
                    'address'                       => $oppo->address,
                    'phone1'                        => $oppo->phone1,
                    'phone2'                        => $oppo->phone2,
                    'fax'                           => $oppo->fax,
                    'incoming_phone'                => $oppo->incoming_phone,
                    'original_incoming_phone'       => $oppo->original_incoming_phone,
                    'negotiation'                   => $oppo->negotiation,
                    'sales'                         => $oppo->sales,
                    'npair_amount'                  => $oppo->npair_amount,
                    'npair_payment_method'          => $oppo->npair_payment_method,
                    'npair_system_registration'     => $oppo->npair_system_registration,
                    'npair_credit_result'           => $oppo->npair_credit_result,
                    'npair_invoice_issued'          => $oppo->npair_invoice_issued,
                    'material_cost'                 => $oppo->material_cost,
                    'material_detail'               => $oppo->material_detail,
                    'material_price'                => $oppo->material_price,
                    'highway_cost'                  => $oppo->highway_cost,
                    'highway_from'                  => $oppo->highway_from,
                    'highway_to'                    => $oppo->highway_to,
                    'parking_cost'                  => $oppo->parking_cost,
                    'stamp_cost'                    => $oppo->stamp_cost,
                    'partner_fee'                   => $oppo->partner_fee,
                    'allowed_cost'                  => $oppo->allowed_cost,
                    'allowed_detail'                => $oppo->allowed_detail,
                    'kurapital_fee'                 => $oppo->kurapital_fee,
                    'other_cost'                    => $oppo->other_cost,
                    'kurapital_cost'                => $oppo->kurapital_cost,
                    'detail'                        => $oppo->detail,
                    'proposal'                      => $oppo->proposal,
                    'kaitori'                       => $oppo->kaitori,
                    'building_name'                 => $oppo->building_name,
                    'shop_name'                     => $oppo->shop_name,
                    'requested_lat_lng'             => $oppo->requested_lat_lng,
                    'last_update_from'              => $oppo->last_update_from == '0000-00-00 00:00:00' ? null : $oppo->last_update_from,
                    'building_room'                 => $oppo->building_room,
                    'reception_number'              => $oppo->reception_number,
                    'order_amount'                  => $oppo->order_amount,
                    'personnel_name'                => $oppo->personnel_name,
                    'accounting_confirmed_amount'   => $oppo->accounting_confirmed_amount,
                    'basic_research_fee'            => $oppo->basic_research_fee,
                    'budget_amount'                 => $oppo->budget_amount,
                    'zeroemi_discount'              => $oppo->zeroemi_discount,
                    'irregular_billing'             => $oppo->irregular_billing,
                    'houzinflg'                     => $oppo->houzinflg,
                    'seisanflg'                     => $oppo->seisanflg,
                    'refere'                        => $oppo->refere,
                    'trouble'                       => $oppo->trouble,
                    'note'                          => $oppo->note,
                    'order_date'                    => $oppo->order_date == '0000-00-00 00:00:00' ? null : $oppo->order_date,
                    'receipt_date'                  => $oppo->receipt_date == '0000-00-00 00:00:00' ? null : $oppo->receipt_date,
                    'work_date'                     => $oppo->work_date == '0000-00-00 00:00:00' ? null : $oppo->work_date,
                    'work_date_end'                 => $oppo->work_date_end == '0000-00-00 00:00:00' ? null : $oppo->work_date_end,
                    'worked_date'                   => $oppo->worked_date == '0000-00-00 00:00:00' ? null : $oppo->worked_date,
                    'status_id'                     => $oppo->status_id,
                    'sub_status_id'                 => $oppo->sub_status_id,
                    'external_collaboration_status_id' => $oppo->external_collaboration_status_id,
                    'company_managed_building_id'   => $oppo->company_managed_building_id,
                    'account_id'                    => $oppo->account_id,
                    'user_id'                       => $oppo->user_id,
                    'owner_id'                      => $oppo->owner_id,
                    'work_content_id'               => $oppo->work_content_id,
                    'work_target_id'                => $oppo->work_target_id,
                    'cancel_reason_id'              => $oppo->cancel_reason_id,
                    'receipt_status_id'             => $oppo->receipt_status_id,
                    'bill_status_id'                => $oppo->bill_status_id,
                    'opportunity_demand_status_id'  => $oppo->opportunity_demand_status_id,
                    'sub_category_id'               => $oppo->sub_category_id,
                    'shift_id'                      => $oppo->shift_id,
                    'on_site_billing_type_id'       => $oppo->on_site_billing_type_id,
                    'on_site_billing_method_id'     => $oppo->on_site_billing_method_id,
                    'tax_rate_id'                   => $oppo->tax_rate_id,
                    'sendmail_account_id'           => $oppo->sendmail_account_id,
                    'kaiin_flyer_id'                => $oppo->kaiin_flyer_id,
                    'not_urikake_flg'               => $oppo->not_urikake_flg,
                    'work_date_recived'             => $oppo->work_date_recived,
                    'other_bill_status_id'          => $oppo->other_bill_status_id,
                    'pay_by_invoice'                => $oppo->pay_by_invoice,
                    'pay_by_service_paper'          => $oppo->pay_by_service_paper,
                    'payed_by_card'                 => $oppo->payed_by_card,
                    'is_additional_projects'        => $oppo->is_additional_projects,
                    'is_completed'                  => $oppo->is_completed,
                    'is_time_designation'           => $oppo->is_time_designation,
                    'is_reschedule'                 => $oppo->is_reschedule,
                    'is_claim'                      => $oppo->is_claim,
                    'is_company'                    => $oppo->is_company,
                    'is_construction'               => $oppo->is_construction,
                    'is_photo_registered'           => $oppo->is_photo_registered,
                    'is_free_time'                  => $oppo->is_free_time,
                    'is_attendance'                 => $oppo->is_attendance,
                    'is_accounting_confirmed'       => $oppo->is_accounting_confirmed,
                    'is_transfer'                   => $oppo->is_transfer,
                    'is_double_window'              => $oppo->is_double_window,
                    'is_emittion'                   => $oppo->is_emittion,
                    'is_irregular_billing'          => $oppo->is_irregular_billing,
                    'sendmail'                      => $oppo->sendmail,
                    'cancel'                        => $oppo->cancel,
                    'created_at'                    => $oppo->created,
                    'updated_at'                    => $oppo->modified,
                ]);
            }
            $start += 2000;
            $end += 2000;
        }
        $opportunity_id = $opportunity->id;
        DB::statement("ALTER TABLE opportunities AUTO_INCREMENT = {$opportunity_id}");
        echo "convOpportunitis() end\n";
    }
}
