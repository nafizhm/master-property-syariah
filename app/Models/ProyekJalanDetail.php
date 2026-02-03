<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekJalanDetail extends Model
{
    use HasFactory;

    protected $table = 'proyek_jalan_detail';

    public $timestamps = false;

    protected $fillable = [
        'id_proyek_jalan',
        'op_ke',
        'tanggal',
        'persen',
        'nilai_pekerjaan',
    ];
}
