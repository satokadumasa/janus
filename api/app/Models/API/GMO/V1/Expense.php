<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'app_expenses';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'expense_type_id',
        'opportunity_id',
        'receipt_id',
        'bill_id',
        'amount',
        'note'
    ];
}
