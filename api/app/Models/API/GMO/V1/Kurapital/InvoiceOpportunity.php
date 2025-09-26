<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

class InvoiceOpportunity extends Model
{
    protected $table = 'app_invoices_opportunities';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'invoice_id',
        'opportunity_id'
    ];

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
        // return $this->belongsTo(Invoice::class);
    }

    public function opportunity()
    {
        // return $this->hasOne(Invoice::class, 'id', 'invoice_id');
        return $this->belongsTo(Opportunity::class);
    }
}
