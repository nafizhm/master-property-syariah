<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPekerjaanBangunan extends Model
{
    use HasFactory;
    protected $table = 'jenis_pekerjaan_bangunan';
    public $timestamps = false;

    protected $fillable = [
        'id_lokasi',
        'jenis',
        'presentasi',
        'harga',
    ];
}
