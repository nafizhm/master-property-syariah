<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPR extends Model
{
    use HasFactory;
    protected $table   = 'spr';
    public $timestamps = false;

    protected $fillable = [
        'id_customer',
        'nama_lengkap',
        'alamat_ktp',
        'no_telp',
        'nik',
        'pekerjaan',
        'nama_perum',
        'tipe_bangunan',
        'kode_kavling',
        'luas_tanah',
        'luas_bangunan',
        'nama_marketing',
        'pic',
        'hrg_jual',
        'biaya_kpr',
        'biaya_custom',
        'diskon',
        'biaya_lain',
        'total_harga_unit',
        'estimasi_pendapatan',
        'join_income',
        'sumber_dana',
        'tujuan_pembelian',
        'pembelian_rumah_ke',
        'nama_proyek',
        'hrg_jual_std',
        'metode_pembayaran',
        'termin_soft',
        'termin_kpr',
        'booking_fee',
        'dp',
        'kewajiban_kredit',
        'catatan',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }
}
