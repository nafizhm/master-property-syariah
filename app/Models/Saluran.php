<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Saluran extends Model
{
    use HasFactory;

    protected $table = 'saluran'; 
    public $timestamps = false;
    
    protected $fillable = [
        'id_lokasi',
        'id_jalan',
        'nama',
        'panjang',
    ];
}