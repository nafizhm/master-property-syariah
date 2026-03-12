<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiKavlingPerusahaan extends Model
{
    protected $table = 'lokasi_kavling_perusahaan';

    public $timestamps = false;

    protected $fillable = [
        'id_lokasi',
        'id_perusahaan'
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }
}
