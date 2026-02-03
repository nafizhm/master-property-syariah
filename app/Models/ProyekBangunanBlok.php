<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekBangunanBlok extends Model
{
    use HasFactory;

    protected $table = 'proyek_bangunan_blok';

    public $timestamps = false;

    protected $fillable = [
        'id_proyek_bangunan',
        'blok',
    ];
}
