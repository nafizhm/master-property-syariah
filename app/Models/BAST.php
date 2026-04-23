<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BAST extends Model
{protected $table = 'bast';
    public $timestamps                          = false;
    protected $fillable                         = [
        'id_customer',
        'tanggal_bast',
        'no_bast',
        'nama_perum',
        'nama_customer',
        'alamat_ktp',
        'nama_jalan',
        'desa',
        'kec',
        'kota',
        'kode_kavling',
        'luas_tanah',
        'luas_bangunan',
        'petugas_pendamping',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }}
