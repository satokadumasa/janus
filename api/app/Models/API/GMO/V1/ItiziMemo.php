<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ItiziMemo extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_itizimemos';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'tel_number',
        'tel_dial',
        'tel_extension',
        'tantou',
        'user_id',
        'created',
        'memotype',
        'naiyou',
        'kensaku_tel1',
        'color',

    ];
    public function ItiziMemo()
    {
        $this->belongsTo(ItiziMemo::class);
    }
    public function ItiziKensakuOppo()
    {
        return $this->hasMany(KensakuOppo::class, 'tel', 'kensaku_tel1');
    }
    public function partners()
    {
        return $this->hasMany(partner::class, 'phone1', 'kensaku_tel1');
    }

    public function getAllMemosAttribute()
    {
        $tel_number = $this->id;
        $ext = $this->tel_extension;
        $kensaku_tel1 = $this->kensaku_tel1;
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
            // $AllMemo['id'] = $memo->id;
            // $AllMemo['tel_number'] = $memo->tel_number;
            // $AllMemo['tel_extension'] = $memo->tel_extension;
            // $AllMemo['tantou'] = $memo->tantou;
            // $AllMemo['user_id'] = $memo->user_id;
            // $AllMemo['memotype'] = $memo->memotype;
            // $AllMemo['naiyou'] = $memo->naiyou;
            // $AllMemo['kensaku_tel1'] = $memo->kensaku_tel1;
            // $AllMemo['created'] = $memo->created;
            // $AllMemo['tel_dial'] = $memo->tel_dial;
            // $totalMemo[] = $AllMemo;
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
    /*
    public function getTypeTwoMemoAttribute()
    {
        $tel_number = $this->id;
        $ext = $this->tel_extension;
        $kensaku_tel1 = $this->kensaku_tel1;
        $memoType = 2;

        $typeTwoMemo = ItiziMemo::query()
            ->where('kensaku_tel1', '=', $kensaku_tel1)
            ->where('tel_extension', '=', $ext)
            ->where('memotype', '=', $memoType)
            ->get();
        $memos = [];

        foreach ($typeTwoMemo as $memo) {
            $memoa = [];
            $memoa['id'] = $memo['id'];
            $memoa['tel_number'] = $memo['tel_number'];
            $memoa['tel_extension'] = $memo['tel_extension'];
            $memoa['tantou'] = $memo['tantou'];
            $memoa['user_id'] = $memo['user_id'];
            $memoa['memotype'] = $memo['memotype'];
            $memoa['naiyou'] = $memo['naiyou'];
            $memoa['kensaku_tel1'] = $memo['kensaku_tel1'];
            $memoa['created'] = $memo['created'];
            $memoa['tel_dial'] = $memo['tel_dial'];
            $memos[] = $memoa;
        }
        return $memos;
    }
    public function getTypeOneMemoAttribute()
    {
        $tel_number = $this->id;
        $ext = $this->tel_extension;
        $kensaku_tel1 = $this->kensaku_tel1;
        $memoType = 1;
        $typeOneMemo = ItiziMemo::query()
            ->where('kensaku_tel1', '=', $kensaku_tel1)
            ->where('tel_extension', '=', $ext)
            ->where('memotype', '=', $memoType)
            ->get();
        $memos = [];
        foreach ($typeOneMemo as $memo) {
            $memoa = [];
            $memoa['id'] = $memo['id'];
            $memoa['tel_number'] = $memo['tel_number'];
            $memoa['tel_extension'] = $memo['tel_extension'];
            $memoa['tantou'] = $memo['tantou'];
            $memoa['user_id'] = $memo['user_id'];
            $memoa['memotype'] = $memo['memotype'];
            $memoa['naiyou'] = $memo['naiyou'];
            $memoa['kensaku_tel1'] = $memo['kensaku_tel1'];
            $memoa['created'] = $memo['created'];
            $memoa['tel_dial'] = $memo['tel_dial'];
            $memos[] = $memoa;
        }
        return $memos;
    }
    public function getTotalMemoAttribute()
    {
        $kensaku_tel1 = $this->kensaku_tel1;
        $memoType = 1;
        $TotalMemo = ItiziMemo::query()
            ->where('kensaku_tel1', '=', $kensaku_tel1)
            ->where('memotype', '=', $memoType)
            ->get();
        $memos = [];
        foreach ($TotalMemo as $memo) {
            $memoa = [];
            $memoa['id'] = $memo['id'];
            $memoa['tel_number'] = $memo['tel_number'];
            $memoa['tel_extension'] = $memo['tel_extension'];
            $memoa['tantou'] = $memo['tantou'];
            $memoa['user_id'] = $memo['user_id'];
            $memoa['memotype'] = $memo['memotype'];
            $memoa['naiyou'] = $memo['naiyou'];
            $memoa['kensaku_tel1'] = $memo['kensaku_tel1'];
            $memoa['created'] = $memo['created'];
            $memoa['tel_dial'] = $memo['tel_dial'];
            $memos[] = $memoa;
        }
        return $memos;
    }
    */
    /*
    public function getCreatedAttribute($value)
    {

        if (!empty($value)) {
            $created = Carbon::parse($value)->format('m-d') ?? null;
            $time = Carbon::parse($value)->format('H:i') ?? null;

            return [
                'formatted' => $created,
                'time'=>$time,
                'created' => $value
            ];
        }
        return null;
    }
    */
}
