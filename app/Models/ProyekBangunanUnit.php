<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekBangunanUnit extends Model
{
    use HasFactory;

    protected $table = 'proyek_bangunan_unit';

    public $timestamps = false;

    protected $fillable = [
        'id_proyek_bangunan',
        'kode_kavling'
    ];

    public function proyekBangunan()
    {
        return $this->belongsTo(ProyekBangunan::class, 'id_proyek_bangunan');
    }
}
