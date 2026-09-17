<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanAnggota extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'nama_kegiatan_user',
        'tanggal_kegiatan',
        'foto',
        'jenis_kegiatan_id',
        'keterangan_kegiatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatan::class, 'jenis_kegiatan_id');
    }
}
