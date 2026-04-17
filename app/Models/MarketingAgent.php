<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingAgent extends Model
{
    use HasFactory;

    protected $table = 'marketing_agent';

    protected $fillable = [
        'kode_agent',
        'nama_agent',
        'jenis_kelamin',
        'alamat',
        'email',
        'no_telp',
        'pekerjaan',
        'sosmed',
        'status',
        'foto',
        'nama_bank',
        'no_rekening',
        'atas_nama',
    ];

    public $timestamps = false;
}
