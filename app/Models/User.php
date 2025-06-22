<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'karyawan_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function sendPasswordResetNotification($token)
    {
        // Jika user bertipe Bagian SDM, kirim hanya ke email user ini
        if ($this->hasRole('Bagian SDM')) {
            $sendToEmail = [$this->email];
        } else {
            // Kirim ke semua email SDM
            $sendToEmail = $this->whereHas('roles', function ($query) {
            $query->where('name', 'Bagian SDM');
            })->pluck('email');
        }

        // Kirim notifikasi ke semua email SDM
        foreach ($sendToEmail as $email) {
            Notification::route('mail', $email)
            ->notify(new CustomResetPassword($token, $this->email));
        }
    }
}
