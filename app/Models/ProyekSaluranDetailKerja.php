<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekSaluranDetailKerja extends Model
{
    use HasFactory;

    protected $table = 'proyek_saluran_detail_kerja';

    public $timestamps = false;

    protected $fillable = [
        'id_proyek_saluran',
        'id_proyek_saluran_detail',
        'id_jenis_pekerjaan',
        'op_lalu',
        'op_sekarang',
    ];
}
