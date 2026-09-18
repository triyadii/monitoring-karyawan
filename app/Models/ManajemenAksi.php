<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ManajemenAksi extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'idAksi',
        'namaAksi',
        'kegiatan',
        'foto',
        'status',
    ];

    protected $casts = [
        'foto' => 'array',
        'status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
