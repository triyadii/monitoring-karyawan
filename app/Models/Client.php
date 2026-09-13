<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'nama',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'nomor_telepon',
        'status_client',
        'sumber_data',
        'idUser',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penginput()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function status()
    {
        return $this->belongsTo(MasterStatusClient::class, 'status_client');
    }
}
