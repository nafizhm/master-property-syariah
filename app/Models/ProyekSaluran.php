<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekSaluran extends Model
{
     use HasFactory;

    protected $table = 'proyek_saluran';

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'no_kontrak',
        'nama_proyek',
        'id_lokasi',
        'id_bank',
        'nama_pemborong',
        'id_saluran',
        'harga_satuan',
        'nilai_pekerjaan',
        'id_bayar',
        'jumlah_termin',
    ];
}
