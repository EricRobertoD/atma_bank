<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomorRekening extends Model
{
    use HasFactory;
    protected $table = 'nomor_rekening';
    protected $primaryKey = 'id_norek';
    protected $fillable = [
        'id_norek',
        'id_user',
        'norek',
        'saldo',
        'jenis_kartu',
    ];
    
    public function User()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    public function Transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_norek');
    }
}
