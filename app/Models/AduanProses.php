<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AduanProses extends Model
{
    use HasFactory;
    protected $table = 'aduan_proses';
    public $timestamps = false;

    protected $fillable = [
        'id_aduan',
        'tgl_update',
        'catatan',
        'stt_proses_aduan',
    ];
}
