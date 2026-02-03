<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jalan extends Model
{
    use HasFactory;

    protected $table = 'jalan';
    public $timestamps = false;

    protected $fillable = [
        'id_lokasi',
        'nama',
        'panjang',
        'lebar',
        'luas',
    ];

    public function lokasi()
    {
        return $this->belongsTo(LokasiKavling::class, 'id_lokasi');
    }
}
