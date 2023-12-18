<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $fillable = [
        'id_norek',
        'id_user',
        'jumlah',
        'jenis_transaksi',
        'tanggal_transaksi',
    ];
    
    public function User()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function NomorRekening()
    {
        return $this->belongsTo(NomorRekening::class, 'id_norek');
    }
}
