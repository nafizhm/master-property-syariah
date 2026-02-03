<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekBangunanDetailKerja extends Model
{
    use HasFactory;

    protected $table = 'proyek_bangunan_detail_kerja';

    public $timestamps = false;

    protected $fillable = [
        'id_proyek_bangunan',
        'id_proyek_bangunan_detail',
        'id_jenis_pekerjaan',
        'op_lalu',
        'op_sekarang',
    ];

}
