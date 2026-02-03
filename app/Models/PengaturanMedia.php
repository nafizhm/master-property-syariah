<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengaturanMedia extends Model
{
    use HasFactory;

    protected $table = 'konfigurasi_media';
    protected $fillable = [
        'jenis_data',
        'nama_file',
    ];

    public $timestamps = false;


}
