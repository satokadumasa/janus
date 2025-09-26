<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'app_banks';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        "id",
        "name",
        "code",
        "store_number",
        "store_name",
        "account_kind",
        "account_number",
        "account_sign",
        "account_name",
        "account_short_name"
    ];
}
