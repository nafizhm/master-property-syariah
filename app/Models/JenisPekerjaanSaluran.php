<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPekerjaanSaluran extends Model
{
    use HasFactory;
    protected $table = 'jenis_pekerjaan_saluran';
    public $timestamps = false;

    protected $fillable = [
        'jenis',
        'presentasi'
    ];            
}
