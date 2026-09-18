<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManajemenVisit extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'namaClient',
        'nomorTelepon',
        'alamat',
        'kegiatan',
        'foto',
        'status_id',
        'user_id',
    ];

    protected $casts = [
        'foto' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(MasterStatusClient::class, 'status_id');
    }
}
