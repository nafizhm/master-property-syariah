<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoProyekBangunan extends Model
{
    protected $table = 'foto_proyek_bangunan';
    public $timestamps = false;
    protected $fillable = ['id_proyek_bangunan_detail', 'foto'];
}
