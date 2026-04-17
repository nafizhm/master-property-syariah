<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komisi extends Model
{
    use HasFactory;
    protected $table = 'komisi';
    public $timestamps = false;

    protected $fillable = [
        'tanggal_komisi',
        'id_bank',
        'id_customer',
        'persen_inhouse',
        'nominal_inhouse',
        'persen_agent',
        'nominal_agent',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }
}
