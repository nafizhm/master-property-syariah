<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar';
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'jenis_proyek',
        'id_proyek_bangunan',
        'id_proyek_jalan',
        'id_proyek_saluran',
        'keterangan',
        'lampiran',
    ];
}
