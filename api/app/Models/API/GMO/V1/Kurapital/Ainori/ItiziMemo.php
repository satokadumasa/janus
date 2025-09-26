<?php

namespace App\Models\API\GMO\V1\Kurapital\Ainori;

use Illuminate\Support\Carbon;
use App\Models\API\GMO\V1\partner;
use Illuminate\Support\Facades\Auth;
use App\Models\API\GMO\V1\KensakuSubAcs;
use Illuminate\Database\Eloquent\Model;

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
    /**
     * Undocumented function
     *
     * @return void
     */
    public function ItiziMemo()
    {
        $this->belongsTo(ItiziMemo::class);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function ItiziKensakuOppo()
    {
        return $this->hasMany(KensakuOppo::class, 'tel', 'kensaku_tel1');
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function partners()
    {
        return $this->hasMany(partner::class, 'phone1', 'kensaku_tel1');
    }

    public function KensakuSubAcs()
    {
        // return $this->hasMany(KensakuSubAcs::class, 'tel', 'tel');
        return $this->hasMany(KensakuSubAcs::class, 'tel', 'kensaku_tel1');
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
        $kensaku_tel1 = $this->kensaku_tel1;
        $memoType = 2;

        $memos = ItiziMemo::query()
            ->where('kensaku_tel1', '=', $kensaku_tel1)
            ->orderBy('created','DESC')
            ->get();
        $totalMemo = [];

        foreach ($memos as $memo) {
            $memo  = (object) $memo;
            $AllMemo = [];
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
            'totalMemoList' => $totalMemo

        ];
    }
    // public function getTotalChakushin(){
    //     return $this->ItiziMemo()->where('');
    // }
}
