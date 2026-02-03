<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekBangunan extends Model
{
    use HasFactory;

    protected $table = 'proyek_bangunan';

    public $timestamps = false;

    protected $fillable = [
        'no_kontrak',
        'tanggal',
        'nama_proyek',
        'nama_pemborong',
        'id_lokasi',
        'tipe_rumah',
        'volume_pekerjaan',
        'harga_satuan',
        'nilai_pekerjaan',
        'id_bank',
        'jumlah_unit',
        'id_bayar',
        'jumlah_termin',
    ];

    public function proyekBangunanDetail()
    {
        return $this->hasMany(ProyekBangunanDetail::class, 'id_proyek_bangunan');
    }
}
