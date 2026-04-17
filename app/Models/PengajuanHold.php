<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanHold extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_hold';

    protected $fillable = [
        'no_registrasi',
        'tgl_booking',
        'nama_lengkap',
        'nik',
        'no_telp',
        'email',
        'alamat_ktp',
        'alamat_domisili',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'npwp',
        'pekerjaan',
        'status_pernikahan',
        'nama_p',
        'nik_p',
        'no_bpjs_kes',
        'nama_saudara',
        'no_telp_saudara',
        'id_marketing',
        'id_agent',
        'id_lokasi',
        'id_kavling',
        'hrg_jual',
        'foto_ktp',
        'foto_npwp',
        'foto_kk',
        'foto_bpjs',
        'foto_pemohon',
        'foto_ktp_p',
        'file_bukti',
        'tgl_booking_fee',
        'booking_fee',
        'diskon',
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
        'jenis_perumahan',
        'jenis_pembelian',
        'id_bank',
        'id_metode_bayar',
        'an_surat_cash',
        'termin_x_cash_b',
        'stt_reg',
    ];

    public $timestamps = false;

    public function marketing()
    {
        return $this->belongsTo(MarketingOffline::class, 'id_marketing');
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
}
