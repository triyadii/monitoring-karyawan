<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JenisPegawai extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    
    protected $fillable = [
        'jenisPegawai',
    ];
}
