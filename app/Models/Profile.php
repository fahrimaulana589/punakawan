<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Illuminate\Database\Eloquent\Casts\Attribute;
class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'handphone',
        'logo',
        'email_server',
        'email_port',
        'email_password',
        'email_username'
    ];

    protected function logoUrl(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            return isset($attributes['logo']) ? asset("storage/".$attributes['logo']) : asset('images/punakawan_logo.png');
        });
    }
}
