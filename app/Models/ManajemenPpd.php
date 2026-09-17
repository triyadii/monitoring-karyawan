<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManajemenPpd extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'namaClient',
        'nomorTelepon',
        'alamat',
        'nomorKontrak',
        'tenor',
        'angsuran',
        'merk',
        'type',
        'jenisKendaraan',
        'pinjaman',
        'jatuhTempo',
        'ktp',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
