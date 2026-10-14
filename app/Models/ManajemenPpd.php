<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'berkas',
        'user_id',
    ];

    protected $casts = [
        'berkas' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
