<?php

namespace App\Models\API\GMO\V1\Kurapital\Ainori;

use App\Models\API\GMO\V1\partner;
use Illuminate\Database\Eloquent\Model;

class SutekoData extends Model
{
    //
  protected $connection = 'gmoCrm';
    protected $table = 'suteko_data';
    public $timestamps = false;

    protected $fillable = [
        'tel_number',
        'tel_category',
        'tel_extension',
        'created',
        'renban',
    ];
    /**
     * Undocumented function
     *
     * @return void
     */
    public function ItiziKensakuOppo()
    {
        return $this->hasMany(KensakuOppo::class, 'tel', 'tel_number');
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function partners()
    {
        return $this->hasMany(partner::class, 'phone1', 'tel_number');
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getAllMemosAttribute()
    {
        $tel_number = $this->id;
        $ext = $this->tel_extension;
        $kensaku_tel1 = $this->tel_number;
        $memoType = 2;

        $memos = ItiziMemo::query()
            ->where('kensaku_tel1', '=', $kensaku_tel1)
            ->where('tel_extension', '=', $ext)
            ->get();
        $memosTypeOne = [];
        $memosTypetwo = [];
        $totalMemo = [];

        foreach ($memos as $memo) {
            $memo  = (object) $memo;
            $AllMemo = [];
            if ($memo->memotype == 1) {
                $typeOne = [];
                $typeOne['id'] = $memo->id;
                $typeOne['tel_number'] = $memo->tel_number;
                $typeOne['tel_extension'] = $memo->tel_extension;
                $typeOne['tantou'] = $memo->tantou;
                $typeOne['user_id'] = $memo->user_id;
                $typeOne['memotype'] = $memo->memotype;
                $typeOne['naiyou'] = $memo->naiyou;
                $typeOne['kensaku_tel1'] = $memo->kensaku_tel1;
                $typeOne['created'] = $memo->created;
                $typeOne['tel_dial'] = $memo->tel_dial;
                $memosTypeOne[] = $typeOne;
            }
            if ($memo->memotype == 2) {
                $TypeTwo = [];
                $TypeTwo['id'] = $memo->id;
                $TypeTwo['tel_number'] = $memo->tel_number;
                $TypeTwo['tel_extension'] = $memo->tel_extension;
                $TypeTwo['tantou'] =  $memo->tantou;
                $TypeTwo['user_id'] = $memo->user_id;
                $TypeTwo['memotype'] = $memo->memotype;
                $TypeTwo['naiyou'] = $memo->naiyou;
                $TypeTwo['kensaku_tel1'] = $memo->kensaku_tel1;
                $TypeTwo['created'] = $memo->created;
                $TypeTwo['tel_dial'] = $memo->tel_dial;
                $memosTypetwo[] = $TypeTwo;
            }
            $AllMemo = [];
            $AllMemo['id'] = $memo['id'];
            $AllMemo['tel_number'] = $memo['tel_number'];
            $AllMemo['tel_extension'] = $memo['tel_extension'];
            $AllMemo['tantou'] = $memo['tantou'];
            $AllMemo['user_id'] = $memo['user_id'];
            $AllMemo['memotype'] = $memo['memotype'];
            $AllMemo['naiyou'] = $memo['naiyou'];
            $AllMemo['kensaku_tel1'] = $memo['kensaku_tel1'];
            $AllMemo['created'] = $memo['created'];
            $AllMemo['tel_dial'] = $memo['tel_dial'];
            $totalMemo[] = $AllMemo;
        }
        return [
            'TypeOneMemoList' => $memosTypeOne,
            'TypeTwoMemoList' => $memosTypetwo,
            'totalMemoList' => $totalMemo

        ];
    }
}
