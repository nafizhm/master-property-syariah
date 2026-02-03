<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingFreelance extends Model
{
    use HasFactory;

    protected $table = 'marketing_freelance';

    protected $fillable = [
        'kode_freelance',
        'nama_freelance',
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
