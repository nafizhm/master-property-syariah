<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianCancel extends Model
{
    use HasFactory;

    protected $table   = 'pembelian_cancel';
    public $timestamps = false;

    protected $fillable = [
        'tgl_batal',
        'id_customer',
        'keterangan_batal',
        'biaya_admin',
        'jumlah_bayar',
        'id_bank',
        'id_bank_tujuan',
        'no_rekening',
        'atas_nama',
        'lampiran_bukti',
        'pinalti',
        'no_cancel',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }
    public function bankTujuan()
    {
        return $this->belongsTo(Bank::class, 'id_bank_tujuan');
    }

}
