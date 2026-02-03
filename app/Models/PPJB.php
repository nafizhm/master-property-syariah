<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PPJB extends Model
{    protected $table = 'ppjb';
    public $timestamps = false;
    protected $fillable = [
        'id_customer',
        'tanggal_ppjb',
        'no_ppjb',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

}
