<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JenisKegiatan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nama_jenis_kegiatan',
    ];
}
