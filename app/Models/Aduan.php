<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    use HasFactory;
    protected $table = 'aduan';
    public $timestamps = false;

    protected $fillable = [
        'no_aduan',
        'tanggal',
        'id_kavling',
        'id_customer',
        'no_kontrak',
        'isi_aduan',
        'stt_aduan',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    public function kavling()
    {
        return $this->belongsTo(KavlingPeta::class, 'id_kavling');
    }
}
