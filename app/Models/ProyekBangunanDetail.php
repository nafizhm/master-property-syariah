<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekBangunanDetail extends Model
{
    use HasFactory;

    protected $table = 'proyek_bangunan_detail';

    public $timestamps = false;

    protected $fillable = [
        'id_proyek_bangunan',
        'op_ke',
        'tanggal',
        'persen',
        'nilai_pekerjaan',
    ];

    public function detailKerja()
    {
        return $this->hasMany(ProyekBangunanDetailKerja::class, 'id_proyek_bangunan_detail');
    }

}
