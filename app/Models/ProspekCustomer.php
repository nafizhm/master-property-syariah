<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspekCustomer extends Model
{
    use HasFactory;

    protected $table = 'prospek_customer';

    protected $fillable = [
        'tgl_terima',
        'nama_lengkap',
        'usia',
        'pekerjaan',
        'penghasilan',
        'sumber_informasi',
        'rangking',
        'id_marketing',
        'id_freelance',
        'keterangan_belum',
        'no_telp',
        'email',
    ];

    public $timestamps = false;

    public function marketing()
    {
        return $this->belongsTo(MarketingOffline::class, 'id_marketing');
    }

    public function freelance()
    {
        return $this->belongsTo(MarketingFreelance::class, 'id_freelance');
    }

}
