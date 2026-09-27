<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'assignable_type',
        'assignable_id',
        'user_id',
        'assigned_by',
        'status_id',
        'catatan',
        'foto'
    ];

    protected $casts = [
        'foto' => 'array',
    ];

    public function assignable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    public function status()
    {
        return $this->belongsTo(MasterStatusClient::class, 'status_id');
    }
}
