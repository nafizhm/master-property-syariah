<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KavlingPeta extends Model
{
    use HasFactory;

    protected $table = 'kavling_peta';

    protected $fillable = [
        'id_lokasi',
        'cluster',
        'blok',
        'no',
        'id_perusahaan',
        'id_cluster',
        'kode_kavling',
        'panjang_kanan',
        'panjang_kiri',
        'lebar_depan',
        'lebar_belakang',
        'luas_tanah',
        'tipe_bangunan',
        'daya_listrik',
        'luas_bangunan',
        'hrg_meter',
        'hrg_jual',
        'peningkatan_mutu',
        'id_rumah_sikumbang',
        'no_sertifikat',
        'jenis_map',
        'map',
        'matrik',
        'status',
        'keterangan',
        'atas_nama_surat',
        'id_customer',
        'tgl_jatuh_tempo',
        'tgl_jatuh_tempo',
        'stt_cicilan',
        'status_ready',
        'foto',
    ];

    public function lokasi()
    {
        return $this->belongsTo(LokasiKavling::class, 'id_lokasi', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id');
    }
    public function progres()
    {
        return $this->belongsTo(ProgresListPenjualan::class, 'status', 'id');
    }

    public function getStatusLabelAttribute()
    {
        if ($this->id_customer && $this->customer && $this->customer->progres) {
            return $this->customer->progres->status_progres;
        }
        return $this->progres->status_progres ?? 'Tersedia';
    }

    public function getSiteplanColorAttribute()
    {
        if ($this->id_customer && $this->customer && $this->customer->progres) {
            return $this->customer->progres->warna;
        }

        if ($this->status == 1) {
             return '#42f202'; // Matching legend for Booking/Pending
        }

        return $this->progres->warna ?? '#ffffff';
    }
     public function unitReady()
    {
        return $this->belongsTo(ProgresUnitReady::class, 'status_ready');
    }
    public function perusahaan() {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }
    public $timestamps = false;
}
