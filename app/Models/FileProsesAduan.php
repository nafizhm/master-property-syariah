<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileProsesAduan extends Model
{
    use HasFactory;
    protected $table = 'file_proses_aduan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_aduan',
        'nama_file',
    ];
}
