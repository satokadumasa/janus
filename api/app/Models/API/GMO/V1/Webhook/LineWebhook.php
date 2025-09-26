<?php

namespace App\Models\API\GMO\V1\Webhook;

use Illuminate\Database\Eloquent\Model;

class LineWebhook extends Model
{
    //
    protected $table = 'linewebhooklogs';
    protected $fillable = [
        'mtype',
        'replyToken',
        'userId',
        'utype',
        'timestamp',
        'message',
        'type',
        'mid',
        'text',
        'destination',
        'ankenid',
        'crmid',
        'receivedata',
        'registered_status'

    ];
}
