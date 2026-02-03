<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoProyekSaluran extends Model
{
    protected $table = 'foto_proyek_saluran';
    public $timestamps = false;
    protected $fillable = ['id_proyek_saluran_detail', 'foto'];
}
