<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekJalanDetailKerja extends Model
{
    use HasFactory;

    protected $table = 'proyek_jalan_detail_kerja';

    public $timestamps = false;

    protected $fillable = [
        'id_proyek_jalan',
        'id_proyek_jalan_detail',
        'id_jenis_pekerjaan',
        'op_lalu',
        'op_sekarang',
    ];
}
