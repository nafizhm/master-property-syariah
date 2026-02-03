<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SerahKunci extends Model
{
    use HasFactory;

    protected $table = 'serah_terima_kunci';
    public $timestamps = false;

    protected $fillable = [
        'id_customer',
        'tgl_serah_terima',
        'tgl_expired',
        'keterangan',
        'status',
    ];

    public function kavling()
    {
        return $this->belongsTo(KavlingPeta::class, 'id_kavling');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }
}
