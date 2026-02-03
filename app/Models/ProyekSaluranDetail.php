<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekSaluranDetail extends Model
{
    use HasFactory;

    protected $table = 'proyek_saluran_detail';

    public $timestamps = false;

    protected $fillable = [
        'id_proyek_saluran',
        'op_ke',
        'tanggal',
        'persen',
        'nilai_pekerjaan',
    ];
}
