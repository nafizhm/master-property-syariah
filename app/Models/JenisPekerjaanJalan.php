<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPekerjaanJalan extends Model
{
    use HasFactory;
    protected $table = 'jenis_pekerjaan_jalan';
    public $timestamps = false;

    protected $fillable = [
        'jenis',
        'presentasi'
    ];            
}
