<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PPJB extends Model
{protected $table = 'ppjb';
    public $timestamps                          = false;
    protected $fillable                         = [
        'id_customer',
        'tanggal_ppjb',
        'no_ppjb',
        'nama_perum',
        'kode_kavling',
        'nama_customer',
        'ttl',
        'alamat_ktp',
        'no_ktp',
        'luas_bangunan',
        'luas_tanah',
        'nama_jalan',
        'desa',
        'kec',
        'kota',
        'prov',
        'no_shm',
        'hrg_jual',
        'diskon',
        'dp',
        'booking_fee',
        'saksi',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }}
