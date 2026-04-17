<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table    = 'customer';
    protected $fillable = [
        'kode_customer',
        'tanggal_verif',
        'id_lokasi',
        'id_kavling',
        'hrg_jual',
        'diskon',
        'id_status_progres',

        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'alamat_ktp',
        'alamat_domisili',
        'status_pernikahan',

        'nama_p',
        'nik_p',
        'no_bpjs_kes',
        'nama_saudara',
        'no_telp_saudara',

        'jenis_perumahan',
        'no_telp',
        'email',
        'npwp',
        'pekerjaan',

        'id_marketing',
        'id_agent',

        'jenis_pembelian',
        'id_bank',
        'id_metode_bayar',
        'an_surat_cash',
        'termin_x_cash_b',

        'pajak_bphtb',
        'stt_free_pajak_bphtb',
        'biaya_notaris',
        'stt_free_biaya_notaris',
        'biaya_kpr',
        'stt_free_biaya_kpr',
        'biaya_custom',
        'biaya_lain_lain',
        'ppn',
        'pajak_pph',
        'bonus_konsumen',
        'total_harga_rumah',
        'total_harga_komisi',

        'stt_arsip',
    ];

    public function ppjb()
    {
        return $this->hasOne(PPJB::class, 'id_customer', 'id');
    }
    public function persyaratan()
    {
        return $this->hasOne(PersyaratanLegal::class, 'id_customer');
    }

    public function marketing()
    {
        return $this->belongsTo(MarketingOffline::class, 'id_marketing');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank');
    }

    public function agent()
    {
        return $this->belongsTo(MarketingAgent::class, 'id_agent');
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiKavling::class, 'id_lokasi');
    }

    public function kavling()
    {
        return $this->belongsTo(KavlingPeta::class, 'id_kavling');
    }

    public function progres()
    {
        return $this->belongsTo(ProgresListPenjualan::class, 'id_status_progres');
    }

    public function lokasiKavling()
    {
        return $this->belongsTo(LokasiKavling::class, 'id_lokasi');
    }

    public function kavlingPeta()
    {
        return $this->belongsTo(KavlingPeta::class, 'id_kavling');
    }

    public function akadDetail()
    {
        return $this->hasMany(AkadDetail::class, 'id_customer');
    }

    public function piutangs()
    {
        return $this->hasMany(Piutang::class, 'id_customer');
    }

    public function pemasukans()
    {
        return $this->hasMany(Pemasukan::class, 'id_customer');
    }

    public function wawancara()
    {
        return $this->hasMany(Wawancara::class, 'id_customer');
    }

    public $timestamps = false;
}
