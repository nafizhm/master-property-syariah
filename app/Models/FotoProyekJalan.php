<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoProyekJalan extends Model
{
    protected $table = 'foto_proyek_jalan';
    public $timestamps = false;
    protected $fillable = ['id_proyek_jalan_detail', 'foto'];
}
