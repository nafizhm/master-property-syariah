<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileAduan extends Model
{
    use HasFactory;

    protected $table = 'file_aduan';
    public $timestamps = false;

    protected $fillable = [
        'id_aduan',
        'id_customer',
        'no_kontrak',
        'nama_file',
    ];

}
