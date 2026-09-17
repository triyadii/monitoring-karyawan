<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

#[Fillable(['role_id', 'jenis_pegawai_id', 'username', 'password', 'nama', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function jenisPegawai()
    {
        return $this->belongsTo(JenisPegawai::class, 'jenis_pegawai_id', 'uuid');
    }

    public function kegiatanAnggotas()
    {
        return $this->hasMany(KegiatanAnggota::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function manajemenAksi()
    {
        return $this->hasMany(ManajemenAksi::class);
    }

    public function manajemenVisit()
    {
        return $this->hasMany(ManajemenVisit::class);
    }

    public function manajemenCanvasing()
    {
        return $this->hasMany(ManajemenCanvasing::class);
    }

    public function manajemenHo()
    {
        return $this->hasMany(ManajemenHo::class);
    }

    public function manajemenPpd()
    {
        return $this->hasMany(ManajemenPpd::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
