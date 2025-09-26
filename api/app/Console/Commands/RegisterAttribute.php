<?php

namespace App\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\API\GMO\V1\Attribute;
use App\Models\API\GMO\V1\AttributeGroup;

class RegisterAttribute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:attribute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'attributesテーブルにデータを登録する';

    /**
     * Create a new command instance.cla
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Attribute::truncate();

        AttributeGroup::truncate();
        // 地方・都道府県
        $this->prefecture();
        // 国コード・国際電話番号
        // $this->countryCode();
        // その他
        $this->other();
    }

    /**
     * その他データ登録
     *
     * 一度登録したvalueおよびparent_valueは変更できません.
     * 変更が必要な場合はテーブルから対象レコードを削除してから再登録してください.
     * nameの変更は可能です.
     *
     * @return void
     */
    public function other()
    {
        $this->add('task_status', 'for accountcandidate', [
            ['value' => '1', 'name' => '全て'],
            ['value' => '2', 'name' => '未完了'],
            ['value' => '3', 'name' => '期限切れ'],
            ['value' => '4', 'name' => '完了済み']
        ]);
        // $this->add('time_management_type', 'for accountcandidate', [
        //     ['value' => '1', 'name' => '全て'],
        //     ['value' => '2', 'name' => '未完了'],
        //     ['value' => '3', 'name' => '期限切れ'],
        //     ['value' => '4', 'name' => '完了済み']
        // ]);
        $this->add('receipt_status', 'receipt status details', [
            ['value' => '1', 'name' => '未領収'],
            ['value' => '2', 'name' => '一部領収'],
            ['value' => '3', 'name' => '領収済'],
        ]);

        $this->add('account_candidate_status_id', 'account_candidate_status_id', [
            ['value' => 1, 'name' => '登録のみ'],
            ['value' => 2, 'name' => '失注・辞退'],
            // ['value' => 3, 'name' => '見込み有り（失注）'],不要
            ['value' => 4, 'name' => '留守'],
            ['value' => 5, 'name' => '担当者不在'],
            ['value' => 6, 'name' => '検討中（架電後）'],
            ['value' => 7, 'name' => '資料送付'],
            ['value' => 8, 'name' => '資料到着確認済み'],
            ['value' => 9, 'name' => '検討中（資料送付後）'],
            ['value' => 10, 'name' => '来社確定'],
            ['value' => 11, 'name' => '検討中（来社後）'],
            ['value' => 12, 'name' => '一部資料不足'],
            ['value' => 13, 'name' => '審査中'],
            ['value' => 14, 'name' => '契約完了'],
            ['value' => 15, 'name' => 'テレビ面談'],
        ]);
        $this->add('opportunity_flgs', '案件の共有事項', [
            ['value' => '1', 'name' => '時間指定'],
            ['value' => '2', 'name' => 'リスケ'],
            ['value' => '3', 'name' => 'クレーム'],
            ['value' => '4', 'name' => '法人案件'],
        ]);

        $this->add('chatwork_id', 'chatwork_ids', [
            ['value' => '1148722', 'name' => '佐藤雄基'],

            ['value' => '1161774', 'name' => '近藤公平'],
            ['value' => '2106462', 'name' => '上堀内真也'],
            ['value' => '3251641', 'name' => '榎本志穂'],
            ['value' => '4009708', 'name' =>  '石崎賢二'],
            ['value' => '4071950', 'name' => '岡村正人'],
            ['value' => '4009715', 'name' => '本庄孝広'],
            ['value' => '1540810', 'name' => '藤井亮人'],
            ['value' => '4499657', 'name' => '神宮寺誠'],
            ['value' => '4499661', 'name' => '藤原優樹'],
        ]);
        $this->add('account_transaction_statuses', 'account_transaction_statuses', [
            ['value' => '1', 'name' =>    '消しこみ前'],
            ['value' => '2', 'name' => '消しこみ済'],
            ['value' => '3', 'name' =>    '対象外'],
        ]);
        $this->add('search_account_transaction_statuses', 'search_account_transaction_statuses', [
            ['value' => '1', 'name' =>    '消しこみ前'],
            ['value' => '2', 'name' => '消しこみ済'],
            ['value' => '3', 'name' =>    '対象外'],
            ['value' => '4', 'name' =>    '全て'],

        ]);
        $this->add('account_schedule_type', 'account_schedule_type', [
            ['value' => '1', 'name' => '動く'],
            ['value' => '2', 'name' => '動かない'],
            ['value' => '3', 'name' => '都度確認'],
        ]);
        $this->add('share_methods', 'Account share methods', [
            ['value' => '1', 'name' => '粗利ベース'],
            ['value' => '2', 'name' => '売上ベース'],
            ['value' => '3', 'name' => '売上ベース(材料費控除あり)'],
        ]);
        $this->add('gender', 'gender', [
            ['value' => '1', 'name' => '男性'],
            ['value' => '2', 'name' => '女性'],
        ]);
        $this->add('generation', 'generation', [
            ['value' => '1', 'name' => '若年層'],
            ['value' => '2', 'name' => '中年層'],
            ['value' => '3', 'name' => '高齢層'],
        ]);
        $this->add('cancel_reason', 'キャンセル理由', [
            ['value' => 1, 'name' => '対応エリア外'],
            ['value' => 2, 'name' => '対応企業不在'],
            ['value' => 3, 'name' => '料金問い合わせ'],
            ['value' => 4, 'name' => 'その他問い合わせ'],
            ['value' => 5, 'name' => '金額が折り合わず'],
            ['value' => 6, 'name' => '時間が折り合わず'],
            ['value' => 7, 'name' => '場所的に対応不可'],
            ['value' => 8, 'name' => '連絡つかず'],
            ['value' => 9, 'name' => '解決済み'],
        ]);

        $this->add('cancel_reason_radio', 'add_by_copy用', [
            ['value' => 1, 'name' => '対応エリア外'],
            ['value' => 2, 'name' => '対応企業不在'],
            ['value' => 3, 'name' => '料金問い合わせ'],
            ['value' => 4, 'name' => 'その他問い合わせ'],
            ['value' => 5, 'name' => '金額が折り合わず'],
            ['value' => 6, 'name' => '時間が折り合わず'],
        ]);

        $this->add('account_candidate_statuses', 'candidate_status', [
            ['value' => '1', 'name' => '登録のみ'],
            ['value' => '2', 'name' => '失注・辞退'],
            ['value' => '3', 'name' => '見込み有り（失注）'],
            ['value' => '4', 'name' => '留守'],
            ['value' => '5', 'name' => '担当者不在'],
            ['value' => '6', 'name' => '検討中（架電後）'],
            ['value' => '7', 'name' => '資料送付'],
            ['value' => '8', 'name' => '資料到着確認済み'],
            ['value' => '9', 'name' => '検討中（資料送付後）'],
            ['value' => '10', 'name' => '来社確定'],
            ['value' => '11', 'name' => '検討中（来社後）'],
            ['value' => '12', 'name' => '一部資料不足'],
            ['value' => '13', 'name' => '審査中'],
            ['value' => '14', 'name' => '契約完了'],
        ]);
        $this->add('claim_statuses', 'claim_statuses', [
            ['value' => 1, 'name' => '未対応'],
            ['value' => 2, 'name' => '対応中'],
            ['value' => 3, 'name' => '対応済']
        ]);
        $this->add('demand_status', 'demand_status', [
            ['value' => 1, 'name' => '問題なし'],
            ['value' => 2, 'name' => '督促難航中'],
            ['value' => 3, 'name' => '係争中'],
            ['value' => 4, 'name' => '対応しない'],
        ]);
        $this->add('bill_status', 'bill_status', [
            ['value' => 1, 'name' => '未清算'],
            ['value' => 2, 'name' => '一部清算'],
            ['value' => 3, 'name' => '清算済'],
        ]);

        $this->add('bill_step', 'bill_status', [
            ['value' => 1, 'name' => '清算前'],
            ['value' => 2, 'name' => '清算中'],
            ['value' => 3, 'name' => '清算完了'],
            ['value' => 4, 'name' => '回収不可'],
            ['value' => 5, 'name' => '係争中'],
        ]);
        $this->add('day_hour', 'hour of the day 一日に２４時間', [
            ["value" => 1, "name" => "1 時"],
            ["value" => 2, "name" => "2 時"],
            ["value" => 3, "name" => "3 時"],
            ["value" => 4, "name" => "4 時"],
            ["value" => 5, "name" => "5 時"],
            ["value" => 6, "name" => "6 時"],
            ["value" => 7, "name" => "7 時"],
            ["value" => 8, "name" => "8 時"],
            ["value" => 9, "name" => "9 時"],
            ["value" => 10, "name" => "10 時"],
            ["value" => 11, "name" => "11 時"],
            ["value" => 12, "name" => "12 時"],
            ["value" => 13, "name" => "13 時"],
            ["value" => 14, "name" => "14 時"],
            ["value" => 15, "name" => "15 時"],
            ["value" => 16, "name" => "16 時"],
            ["value" => 17, "name" => "17 時"],
            ["value" => 18, "name" => "18 時"],
            ["value" => 19, "name" => "19 時"],
            ["value" => 20, "name" => "20 時"],
            ["value" => 21, "name" => "21 時"],
            ["value" => 22, "name" => "22 時"],
            ["value" => 23, "name" => "23 時"],
            ["value" => 24, "name" => "0 時"]
        ]);
        $this->add('work_minute', '30分でくぎりました分', [
            ["value" => 0, "name" => "0 分"],
            ["value" => 30, "name" => "30 分"],

        ]);
        $this->add('call_type', 'call_type', [
            ['value' => 1, 'name' => '新規問合せ'],
            ['value' => 2, 'name' => '既存客からの連絡'],
            ['value' => 3, 'name' => 'パートナーからの連絡'],
            ['value' => 4, 'name' => '提携法人からの連絡'],
            ['value' => 5, 'name' => '営業電話・間違い電話'],
            ['value' => 6, 'name' => 'その他'],
        ]);
        $this->add('fields', '分野', [
            ['value' => 1, 'name' => '鍵'],
            ['value' => 2, 'name' => '水まわり'],
            ['value' => 3, 'name' => 'ガラス'],
            ['value' => 4, 'name' => 'パソコン'],
            ['value' => 5, 'name' => '電気工事'],
            ['value' => 6, 'name' => '内装'],
            ['value' => 7, 'name' => 'ハウスクリーニング'],
            ['value' => 8, 'name' => '害虫駆除'],
            ['value' => 9, 'name' => '屋根'],
            ['value' => 10, 'name' => '庭'],
            ['value' => 11, 'name' => 'エアコン'],
            ['value' => 12, 'name' => 'リノベ'],
            ['value' => 13, 'name' => 'ペット葬儀'],
            ['value' => 14, 'name' => '盗聴器発見'],
            ['value' => 15, 'name' => '家電'],
        ]);
        $this->add('payment_method', '分野', [
            ['value' => 1, 'name' => '現金払い'],
            ['value' => 2, 'name' => 'カード払い'],
            ['value' => 3, 'name' => '請求書払い'],
            ['value' => 4, 'name' => 'その他'],
            ['value' => 5, 'name' => 'サービス書払い'],
            ['value' => 6, 'name' => '住まペイ'],
            ['value' => 7, 'name' => 'テレコムクレジット'],
            ['value' => 8, 'name' => 'omise'],
            ['value' => 9, 'name' => 'PayPal'],
            ['value' => 10, 'name' => 'スクエア'],
            ['value' => 11, 'name' => 'NP後払い'],
        ]);

        $this->add('card_payment_method', '分野', [
            ['value' => 8, 'name' => 'omise'],
            ['value' => 9, 'name' => 'PayPal'],
            ['value' => 10, 'name' => 'スクエア']
        ]);

        $this->add('receive_card_payment_method', '領収状況の領収処理ページ専用', [
            ['value' => 1, 'name' => '現金払い'],
            ['value' => 3, 'name' => '請求書払い'],
            ['value' => 7, 'name' => 'テレコムクレジット'],
            ['value' => 8, 'name' => 'omise'],
            ['value' => 9, 'name' => 'PayPal'],
            ['value' => 10, 'name' => 'スクエア']
        ]);
        // ['value' => 5, 'name' => 'サービス書払い'],

        $this->add('account_prefecture', 'account_prefecture', [
            ['value' => 1, 'name' => '北海道'],
            ['value' => 2, 'name' => '青森県'],
            ['value' => 3, 'name' => '岩手県'],
            ['value' => 4, 'name' => '宮城県'],
            ['value' => 5, 'name' => '秋田県'],
            ['value' => 6, 'name' => '山形県'],
            ['value' => 7, 'name' => '福島県'],
            ['value' => 8, 'name' => '茨城県'],
            ['value' => 9, 'name' => '栃木県'],
            ['value' => 10, 'name' => '群馬県'],
            ['value' => 11, 'name' => '埼玉県'],
            ['value' => 12, 'name' => '千葉県'],
            ['value' => 13, 'name' => '東京都'],
            ['value' => 14, 'name' => '神奈川県'],
            ['value' => 15, 'name' => '新潟県'],
            ['value' => 16, 'name' => '富山県'],
            ['value' => 17, 'name' => '石川県'],
            ['value' => 18, 'name' => '福井県'],
            ['value' => 19, 'name' => '山梨県'],
            ['value' => 20, 'name' => '長野県'],
            ['value' => 21, 'name' => '岐阜県'],
            ['value' => 22, 'name' => '静岡県'],
            ['value' => 23, 'name' => '愛知県'],
            ['value' => 24, 'name' => '三重県'],
            ['value' => 25, 'name' => '滋賀県'],
            ['value' => 26, 'name' => '京都府'],
            ['value' => 27, 'name' => '大阪府'],
            ['value' => 28, 'name' => '兵庫県'],
            ['value' => 29, 'name' => '奈良県'],
            ['value' => 30, 'name' => '和歌山県'],
            ['value' => 31, 'name' => '鳥取県'],
            ['value' => 32, 'name' => '島根県'],
            ['value' => 33, 'name' => '岡山県'],
            ['value' => 34, 'name' => '広島県'],
            ['value' => 35, 'name' => '山口県'],
            ['value' => 36, 'name' => '徳島県'],
            ['value' => 37, 'name' => '香川県'],
            ['value' => 38, 'name' => '愛媛県'],
            ['value' => 39, 'name' => '高知県'],
            ['value' => 40, 'name' => '福岡県'],
            ['value' => 41, 'name' => '佐賀県'],
            ['value' => 42, 'name' => '長崎県'],
            ['value' => 43, 'name' => '熊本県'],
            ['value' => 44, 'name' => '大分県'],
            ['value' => 45, 'name' => '宮崎県'],
            ['value' => 46, 'name' => '鹿児島県'],
            ['value' => 47, 'name' => '沖縄県'],
        ]);
        $this->add('work_target', 'work_target', [
            ['value' => '1', 'name' => '建物のドア', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '2', 'name' => '建物の引き戸や窓', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '3', 'name' => '国産車', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '4', 'name' => '外国車', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '5', 'name' => '国産バイク', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '6', 'name' => '外国製バイク', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '7', 'name' => '家庭用金庫', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '8', 'name' => '業務用金庫', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '9', 'name' => 'スーツケース類', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '10', 'name' => 'ロッカー・キャビネ・机', 'parent_group' => 'fields', 'parent_value' => '1'],
            ['value' => '21', 'name' => 'トイレつまり', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '22', 'name' => 'キッチンつまり', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '23', 'name' => 'その他排水つまり', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '24', 'name' => '水栓水漏れ', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '25', 'name' => '水栓交換', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '26', 'name' => 'トイレ水漏れ', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '27', 'name' => '排水管水漏れ', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '28', 'name' => '漏水', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '29', 'name' => 'ウォシュレット', 'parent_group' => 'fields', 'parent_value' => '2'],
            ['value' => '30', 'name' => '網入ガラス', 'parent_group' => 'fields', 'parent_value' => '3'],
            ['value' => '31', 'name' => '網なしガラス', 'parent_group' => 'fields', 'parent_value' => '3'],
            ['value' => '32', 'name' => 'ペアガラス', 'parent_group' => 'fields', 'parent_value' => '3'],
            ['value' => '33', 'name' => '大型ガラス', 'parent_group' => 'fields', 'parent_value' => '3'],
            ['value' => '34', 'name' => '鏡', 'parent_group' => 'fields', 'parent_value' => '3'],
            ['value' => '35', 'name' => 'ショーケース', 'parent_group' => 'fields', 'parent_value' => '3'],
            ['value' => '11', 'name' => 'スイッチ', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '12', 'name' => 'コンセント', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '13', 'name' => '照明', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '14', 'name' => '換気扇', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '15', 'name' => '漏電', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '16', 'name' => '家電修理', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '17', 'name' => '分電盤(ブレーカー)', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '18', 'name' => 'アンテナ', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '19', 'name' => 'インターホン', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '20', 'name' => 'エアコン', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '64', 'name' => 'レンジフード', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '65', 'name' => '給湯器', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '66', 'name' => '契約変更', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '67', 'name' => '看板', 'parent_group' => 'fields', 'parent_value' => '5'],
            ['value' => '60', 'name' => '家庭用エアコン', 'parent_group' => 'fields', 'parent_value' => '11'],
            ['value' => '61', 'name' => '業務用エアコン', 'parent_group' => 'fields', 'parent_value' => '11'],
            ['value' => '62', 'name' => '家庭用(天カセ)', 'parent_group' => 'fields', 'parent_value' => '11'],
            ['value' => '63', 'name' => '家庭用(マルチ)', 'parent_group' => 'fields', 'parent_value' => '11'],
            ['value' => '36', 'name' => '起動トラブル', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '37', 'name' => 'ウィルス', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '38', 'name' => 'PCデータ復旧', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '39', 'name' => 'スマホデータ復旧', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '40', 'name' => 'USB・SDカードデータ復旧', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '41', 'name' => '外付HDDデータ復旧', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '42', 'name' => 'ネット接続', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '43', 'name' => '設定', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '44', 'name' => '部品故障', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '45', 'name' => '液晶割れ', 'parent_group' => 'fields', 'parent_value' => '4'],
            ['value' => '46', 'name' => 'ネズミ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '47', 'name' => 'スズメバチ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '48', 'name' => 'アシナガバチ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '49', 'name' => 'ミツバチ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '50', 'name' => 'その他ハチ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '51', 'name' => '黒アリ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '52', 'name' => '白アリ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '53', 'name' => '羽アリ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '54', 'name' => 'ダニ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '55', 'name' => 'ハクビシン', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '56', 'name' => 'ゴキブリ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '57', 'name' => 'ハエ', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '58', 'name' => '毛虫', 'parent_group' => 'fields', 'parent_value' => '8'],
            ['value' => '59', 'name' => 'ムカデ', 'parent_group' => 'fields', 'parent_value' => '8'],
        ]);

        $this->add('work_content', 'work_content', [
            ['value' => '1', 'name' => '鍵開け', 'parent_value' => '1'],
            ['value' => '2', 'name' => '鍵作成（イモビ）', 'parent_value' => '1'],
            ['value' => '3', 'name' => '鍵作成（ディンプル）', 'parent_value' => '1'],
            ['value' => '4', 'name' => '鍵作成（外溝）', 'parent_value' => '1'],
            ['value' => '5', 'name' => '鍵作成（特殊）', 'parent_value' => '1'],
            ['value' => '6', 'name' => '鍵交換', 'parent_value' => '1'],
            ['value' => '7', 'name' => '鍵修理', 'parent_value' => '1'],
            ['value' => '8', 'name' => '鍵新規取付け', 'parent_value' => '1'],
            ['value' => '9', 'name' => '合鍵作成（マスターキーあり）', 'parent_value' => '1'],
            ['value' => '10', 'name' => '取付け', 'parent_value' => '11'],
        ]);

        $this->add('opportunity_status', 'ステータス', [
            ['value' => '1', 'name' => 'メール未送信'],
            ['value' => '2', 'name' => '未連絡'],
            ['value' => '3', 'name' => '商談中'],
            ['value' => '4', 'name' => '出張確定'],
            ['value' => '5', 'name' => '工事完了'],
            ['value' => '6', 'name' => '失注'],
            ['value' => '7', 'name' => '請求中'],
            ['value' => '8', 'name' => '入金済'],
            ['value' => '9', 'name' => 'キャンセル督促'],
            ['value' => '10', 'name' => '追いかけ中'],
            ['value' => '21', 'name' => 'クレーム案件'],
            ['value' => '12', 'name' => 'お客様都合キャンセル'],

        ]);
        $this->add('dial', '屋号', [
            ['value' => '1', 'name' => '976', 'ads' => 'クラピタル'],
            ['value' => '2', 'name' => '364', 'ads' => 'クラピタル'],
            ['value' => '3', 'name' => '604', 'ads' => 'クラピタル'],
            ['value' => '4', 'name' => '189', 'ads' => 'クラピタル'],
            ['value' => '5', 'name' => '009', 'ads' => 'クラピタル'],
            ['value' => '6', 'name' => '4_Denki110', 'ads' => 'クラピタル'],
            ['value' => '7', 'name' => 'Place',    'ads' => 'クラピタル'],
            ['value' => '8', 'name' => 'Mikata', 'ads' => 'クラピタル'],
            ['value' => '9', 'name' => 'Houjin', 'ads' => 'クラピタル'],
            ['value' => '10', 'name' => '589', 'ads' => 'クラピタル'],
            ['value' => '11', 'name' => '820', 'ads' => 'クラピタル'],
            ['value' => '12', 'name' => '160', 'ads' => 'クラピタル'],
            ['value' => '13', 'name' => '616', 'ads' => 'クラピタル'],
            ['value' => '14', 'name' => '998', 'ads' => 'クラピタル'],
            ['value' => '25', 'name' => '1_Denki110', 'ads' => '電気の救急車'],
            ['value' => '26', 'name' => 'Life99', 'ads' => 'ライフ救急車'],
            ['value' => '27', 'name' => 'Shuriya', 'ads' => '街の修理屋さん'],
            ['value' => '28', 'name' => '2_Denki110', 'ads' => '電気の救急車'],
            ['value' => '29', 'name' => 'DFC'],
            ['value' => '30', 'name' => 'Shuriya24', 'ads' => '修理屋24'],
            ['value' => '31', 'name' => 'affi_kurapital', 'ads' => 'クラピタル'],
            ['value' => '32', 'name' => 'hikaku_life99', 'ads' => 'ライフ救急車'],
            ['value' => '33', 'name' => 'hikaku_shuriya', 'ads' => '街の修理屋さん'],
            ['value' => '34', 'name' => 'Denki110 aircon', 'ads' => '電気の救急車'],
            ['value' => '35', 'name' => 'hikaku_24', 'ads' => '修理屋24'],
            ['value' => '36', 'name' => 'Trouble99', 'ads' => 'トラブル99'],
            ['value' => '37', 'name' => '3_Denki110', 'ads' => '電気の救急車'],
            ['value' => '38', 'name' => 'Life99 Aircon', 'ads' => 'ライフ救急車'],
            ['value' => '39', 'name' => 'Shuriya Aircon', 'ads' => '街の修理屋さん'],
            ['value' => '40', 'name' => 'Support99', 'ads' => 'サポート99'],
            ['value' => '41', 'name' => '2_Support99', 'ads' => 'サポート99 サテライト2'],
            ['value' => '42', 'name' => '3_Support99', 'ads' => 'サポート99 サテライト3'],
            ['value' => '50', 'name' => 'Rescue', 'ads' => 'Rescue'],
            ['value' => '61', 'name' => 'Shuriya_seo', 'ads' => 'Rescue'],

        ]);
        $this->add('search_dial', '屋号search', [
            ['value' => '1', 'name' => '976', 'ads' => 'クラピタル'],
            ['value' => '2', 'name' => '364', 'ads' => 'クラピタル'],
            ['value' => '3', 'name' => '604', 'ads' => 'クラピタル'],
            ['value' => '4', 'name' => '189', 'ads' => 'クラピタル'],
            ['value' => '5', 'name' => '009', 'ads' => 'クラピタル'],
            ['value' => '6', 'name' => '4_Denki110', 'ads' => 'クラピタル'],
            ['value' => '7', 'name' => 'Place',    'ads' => 'クラピタル'],
            ['value' => '8', 'name' => 'Mikata', 'ads' => 'クラピタル'],
            ['value' => '9', 'name' => 'Houjin', 'ads' => 'クラピタル'],
            ['value' => '10', 'name' => '589', 'ads' => 'クラピタル'],
            ['value' => '11', 'name' => '820', 'ads' => 'クラピタル'],
            ['value' => '12', 'name' => '160', 'ads' => 'クラピタル'],
            ['value' => '13', 'name' => '616', 'ads' => 'クラピタル'],
            ['value' => '14', 'name' => '998', 'ads' => 'クラピタル'],
            ['value' => '25', 'name' => '1_Denki110', 'ads' => '電気の救急車'],
            ['value' => '26', 'name' => 'Life99', 'ads' => 'ライフ救急車'],
            ['value' => '27', 'name' => 'Shuriya', 'ads' => '街の修理屋さん'],
            ['value' => '28', 'name' => '2_Denki110', 'ads' => '電気の救急車'],
            ['value' => '29', 'name' => 'DFC'],
            ['value' => '30', 'name' => 'Shuriya24', 'ads' => '修理屋24'],
            ['value' => '31', 'name' => 'affi_kurapital', 'ads' => 'クラピタル'],
            ['value' => '32', 'name' => 'hikaku_life99', 'ads' => 'ライフ救急車'],
            ['value' => '33', 'name' => 'hikaku_shuriya', 'ads' => '街の修理屋さん'],
            ['value' => '34', 'name' => 'Denki110 aircon', 'ads' => '電気の救急車'],
            ['value' => '35', 'name' => 'hikaku_24', 'ads' => '修理屋24'],
            ['value' => '36', 'name' => 'Trouble99', 'ads' => 'トラブル99'],
            ['value' => '37', 'name' => '3_Denki110', 'ads' => '電気の救急車'],
            ['value' => '38', 'name' => 'Life99 Aircon', 'ads' => 'ライフ救急車'],
            ['value' => '39', 'name' => 'Shuriya Aircon', 'ads' => '街の修理屋さん'],
            ['value' => '40', 'name' => 'Support99', 'ads' => 'サポート99'],
            ['value' => '41', 'name' => '2_Support99', 'ads' => 'サポート99 サテライト2'],
            ['value' => '42', 'name' => '3_Support99', 'ads' => 'サポート99 サテライト3'],
            ['value' => '50', 'name' => 'Rescue', 'ads' => 'Rescue'],
            ['value' => '61', 'name' => 'Shuriya_seo', 'ads' => 'Rescue'],
            ['value' => 'null', 'name' => 'null', 'ads' => 'Rescue'],
        ]);
        $this->add('estamation_template', 'estamation_template', [
            ['value' => '1', 'name' =>    'ARS 見積書・発注書(1枚)'],
            ['value' => '2', 'name' => 'ARS 見積書・発注書(明細別)'],
            ['value' => '3', 'name' =>    'ARS 見積書のみ'],
            ['value' => '11', 'name' =>    'リュウセン 見積書・発注書(1枚)'],
            ['value' => '12', 'name' => 'リュウセン 見積書・発注書(明細別)'],
            ['value' => '13', 'name' =>    'リュウセン 見積書のみ'],
            ['value' => '21', 'name' =>    '電気の救急車 見積書・発注書(1枚)'],
            ['value' => '22', 'name' => '電気の救急車 見積書・発注書(明細別)'],
            ['value' => '23', 'name' =>    '電気の救急車 見積書のみ'],
            ['value' => '31', 'name' =>    'JCC 見積書・発注書(1枚)'],
            ['value' => '32', 'name' =>    'JCC 見積書・発注書(明細別)'],
            ['value' => '33', 'name' => 'JCC 見積書のみ'],

        ]);

        //下のコメントは今はいらない
        $this->add('invoiceTemplates', '請求書のテンプレート一覧', [
            ['value' => '1', 'name' => 'ARSの請求書(1枚)'],
            ['value' => '2', 'name' => 'ARSの請求書(明細別)'],
            // ['value' => '11', 'name' => 'リュウセンの請求書(1枚)'],
            // ['value' => '12', 'name' => 'リュウセンの請求書(明細別)'],
            // ['value' => '21', 'name' => '電気の救急車の請求書(1枚)'],
            // ['value' => '22', 'name' => '電気の救急車の請求書(明細別)'],
            // ['value' => '31', 'name' => 'JCCの請求書(1枚)'],
            // ['value' => '32', 'name' => 'JCCの請求書(明細別)']
        ]);

        $this->add('contractCompanies', '契約会社一覧', [
            ['value' => '1', 'name' => '株式会社ARS'],
            ['value' => '2', 'name' => '株式会社リュウセン'],
            ['value' => '3', 'name' => '有限会社JCC'],
        ]);

        $this->add('receivedStatus', '領収のステータス', [
            ['value' => '0', 'name' => '未領収'],
            ['value' => '1', 'name' => '領収済'],
        ]);

        $this->add('irrecoverable', '貸し倒れのステータス', [
            ['value' => '0', 'name' => '回収可能'],
            ['value' => '1', 'name' => '回収不可'],
        ]);

        $this->add('byKurapital', '領収者', [
            ['value' => '0', 'name' => 'パートナー'],
            ['value' => '1', 'name' => 'クラピタル'],
        ]);

        $this->add('banks', '銀行口座', [
            ['value' => '1', 'name' => '三菱東京UFJ銀行'],
            ['value' => '2', 'name' => 'ゆうちょ銀行'],
            ['value' => '3', 'name' => '楽天銀行(第一) (ARS)'],
            ['value' => '4', 'name' => '楽天銀行(第二)'],
            ['value' => '5', 'name' => '楽天銀行(第一) (リュウセン)'],
            ['value' => '6', 'name' => '住信ＳＢＩネット銀行'],
        ]);

        $this->add('approved_user', '売り掛け許可した人, 請求書の担当', [
            ['value' => '2', 'name' => '河村'],
            ['value' => '18', 'name' => '津田']
        ]);

        $this->add('receipt_plan_status', '領収予定を確認したかどうか', [
            ['value' => '1', 'name' => '要確認'],
            ['value' => '2', 'name' => '確認済']
        ]);

        $this->add('billing_method', 'その他領収一覧の請求方法', [
            ['value' => '1', 'name' => '一見法人（請求書）'],
            ['value' => '2', 'name' => '提携法人'],
            ['value' => '3', 'name' => 'クレジット'],
            ['value' => '4', 'name' => 'クレジット(買取)'],
            // ['value' => '5', 'name' => 'サービス書'],
            // ['value' => '6', 'name' => 'イレギュラー清算'],
            ['value' => '7', 'name' => 'それ以外'],
            ['value' => '8', 'name' => '一見法人（後払い）']
        ]);

        $this->add('other_receipt_statuses', 'その他領収の案件の請求ステータス', [
            ['value' => '0', 'name' => '一覧未登録'],
            ['value' => '1', 'name' => '未請求'],
            ['value' => '2', 'name' => '請求済'],
            ['value' => '3', 'name' => '入金済'],
            ['value' => '4', 'name' => '督促中']
        ]);

        $this->add('other_bill_statuses', '請求書・クレカ一覧の請求ステータス', [
            ['value' => '1', 'name' => '請求書作成済'],
            ['value' => '2', 'name' => '請求書送付済'],
            ['value' => '3', 'name' => '入金済'],
            ['value' => '4', 'name' => '督促中'],
            ['value' => '5', 'name' => '一部未請求'],
            ['value' => '6', 'name' => '請求不足金有り']
        ]);

        $this->add('other_bill_sorting', '請求書・クレカの仕分け', [
            ['value' => '0', 'name' => '請求する請求書・クレカ'],
            ['value' => '1', 'name' => '請求対象外'],
            ['value' => '2', 'name' => '全て表示する'],
        ]);

        $this->add('other_billing_group', '請求書かクレカのフィルタリング', [
            ['value' => '1', 'name' => '請求書'],
            ['value' => '2', 'name' => 'クレカ'],
        ]);

        $this->add('payment_sites_deadline', '支払サイトの締め日', [
            ['value' => '31', 'name' => '月末'],
            ['value' => '20', 'name' => '20日'],
            ['value' => '15', 'name' => '15日'],
            ['value' => '10', 'name' => '10日'],
            ['value' => '1', 'name' => '1日'],
            ['value' => '2', 'name' => '2日'],
            ['value' => '3', 'name' => '3日'],
            ['value' => '4', 'name' => '4日'],
            ['value' => '5', 'name' => '5日'],
            ['value' => '6', 'name' => '6日'],
            ['value' => '7', 'name' => '7日'],
            ['value' => '8', 'name' => '8日'],
            ['value' => '9', 'name' => '9日'],
            ['value' => '11', 'name' => '11日'],
            ['value' => '12', 'name' => '12日'],
            ['value' => '13', 'name' => '13日'],
            ['value' => '14', 'name' => '14日'],
            ['value' => '16', 'name' => '16日'],
            ['value' => '17', 'name' => '17日'],
            ['value' => '18', 'name' => '18日'],
            ['value' => '19', 'name' => '19日'],
            ['value' => '21', 'name' => '21日'],
            ['value' => '22', 'name' => '22日'],
            ['value' => '23', 'name' => '23日'],
            ['value' => '24', 'name' => '24日'],
            ['value' => '25', 'name' => '25日'],
            ['value' => '26', 'name' => '26日'],
            ['value' => '27', 'name' => '27日'],
            ['value' => '28', 'name' => '28日'],
            ['value' => '29', 'name' => '29日'],
            ['value' => '30', 'name' => '30日'],
        ]);

        $this->add('payment_month_group', '支払月', [
            ['value' => '1', 'name' => '翌月'],
            ['value' => '2', 'name' => '翌々月'],
        ]);

        $this->add('houjin_group', '一見法人, 提携法人', [
            ['value' => '1', 'name' => '提携法人'],
            ['value' => '2', 'name' => '一見法人'],
        ]);

        $this->add('payment_date', '支払日', [
            ['value' => '31', 'name' => '末'],
            ['value' => '20', 'name' => '20日'],
            ['value' => '15', 'name' => '15日'],
            ['value' => '10', 'name' => '10日'],
            ['value' => '1', 'name' => '1日'],
            ['value' => '2', 'name' => '2日'],
            ['value' => '3', 'name' => '3日'],
            ['value' => '4', 'name' => '4日'],
            ['value' => '5', 'name' => '5日'],
            ['value' => '6', 'name' => '6日'],
            ['value' => '7', 'name' => '7日'],
            ['value' => '8', 'name' => '8日'],
            ['value' => '9', 'name' => '9日'],
            ['value' => '11', 'name' => '11日'],
            ['value' => '12', 'name' => '12日'],
            ['value' => '13', 'name' => '13日'],
            ['value' => '14', 'name' => '14日'],
            ['value' => '16', 'name' => '16日'],
            ['value' => '17', 'name' => '17日'],
            ['value' => '18', 'name' => '18日'],
            ['value' => '19', 'name' => '19日'],
            ['value' => '21', 'name' => '21日'],
            ['value' => '22', 'name' => '22日'],
            ['value' => '23', 'name' => '23日'],
            ['value' => '24', 'name' => '24日'],
            ['value' => '25', 'name' => '25日'],
            ['value' => '26', 'name' => '26日'],
            ['value' => '27', 'name' => '27日'],
            ['value' => '28', 'name' => '28日'],
            ['value' => '29', 'name' => '29日'],
            ['value' => '30', 'name' => '30日'],
        ]);

        $this->add('group_name', 'ユーザーのグループ', [
            ['value' => 1, 'name' => '管理者'],
            ['value' => 2, 'name' => '正社員'],
            ['value' => 3, 'name' => 'アルバイト'],
        ]);

    }

    /**
     * 地方・都道府県データ登録
     *
     * @return void
     */
    public function prefecture()
    {
        $commonData = include base_path('master/common.php');
        $regions = Arr::get($commonData, 'prefecture.regions');
        $regionData = [];
        $prefectureData = [];
        foreach ($regions as $name => $region) {
            $regionData[] = ['value' => $region['value'], 'name' => $name];
            foreach ($region['pref'] as $name => $pref) {
                $prefectureData[] = ['value' => $pref['value'], 'name' => $name, 'parent_group' => 'region', 'parent_value' => $region['value']];
            }
        }
        $this->add('region', '地方', $regionData);
        $this->add('prefecture', '都道府県', $prefectureData);
    }

    /**
     * 国コード・国際電話番号データ登録
     *
     * @return void
     */
    public function countryCode()
    {
        $phoneData = include base_path('master/phone.php');
        $countries = Arr::get($phoneData, 'format.country_code');
        $country_code = [];
        $country_calling_code = [];
        foreach ($countries as $code => $data) {
            $name = strtolower($data['ja']);
            $name = trim(preg_replace('/\(.+?\)/', '', $name));
            $name = str_replace('st.', 'saint', $name);
            $name = str_replace(',', '', $name);
            $name = str_replace('\'', '', $name);
            $name = str_replace('-', '_', $name);
            $name = str_replace('&amp;', 'and', $name);
            $name = preg_replace('/\/.+$/', '', $name);
            $name = preg_replace('/\s+/', '_', $name);
            $country_code[] = ['value' => $code, 'name' => $name];
            $country_calling_code[] = ['value' => (int) $data['value'], 'name' => $name];
        }
        $this->add('country_code', '国コード', $country_code);
        $this->add('country_calling_code', '国際電話番号', $country_calling_code);
    }

    /**
     * 属性追加
     *
     * @param string $group
     * @paramg $description
     * @param array $attributes
     */
    public function add($group, $description, $attributes)
    {
        DB::transaction(function () use ($group, $description, $attributes) {
            $groupRow = DB::table('attribute_groups')
                ->where('name', $group)
                ->lockForUpdate()
                ->first();

            if (empty($groupRow)) {
                $groupId = DB::table('attribute_groups')
                    ->insertGetId([
                        'name' => $group,
                        'description' => $description,
                        'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                    ]);
            } else {
                $groupId = $groupRow->id;
                DB::table('attribute_groups')
                    ->where('id', $groupId)
                    ->update([
                        'description' => $description,
                        'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
                    ]);
            }

            $attributeRow = DB::table('attributes')
                ->select(DB::raw('MAX(display_order) AS max'))
                ->where('group_id', $groupId)
                ->lockForUpdate()
                ->first();

            $displayOrder = 0;

            foreach ($attributes as $data) {
                $attributeRow = DB::table('attributes')
                    ->where('group_id', $groupId)
                    ->where('value', $data['value'])
                    ->first();

                if (!empty($data['parent_group'])) {
                    $parentGroupRow = DB::table('attribute_groups')
                        ->where('name', $data['parent_group'])
                        ->first();
                    if (empty($parentGroupRow)) {
                        continue;
                    }
                    $parentGroupId = $parentGroupRow->id;
                }

                ++$displayOrder;

                if (empty($attributeRow)) {
                    DB::table('attributes')
                        ->insert([
                            'group_id' => $groupId,
                            'value' => $data['value'],
                            'name' => $data['name'],
                            'parent_group_id' => (!empty($parentGroupId)) ? $parentGroupId : null,
                            'parent_value' => (!empty($data['parent_value'])) ? $data['parent_value'] : null,
                            'display_order' => $displayOrder,
                            'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                        ]);
                } else {
                    $attributeId = $attributeRow->id;
                    DB::table('attributes')
                        ->where('id', $attributeId)
                        ->update([
                            'name' => $data['name'],
                            'parent_group_id' => (!empty($parentGroupId)) ? $parentGroupId : null,
                            'parent_value' => (!empty($data['parent_value'])) ? $data['parent_value'] : null,
                            'display_order' => $displayOrder,
                            'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
                        ]);
                }
            }
        }, 3);
    }
}
