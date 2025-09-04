<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'payment_date',
        'payment_cut_off_date',
        'amount',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
