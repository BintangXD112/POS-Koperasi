<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function isAdmin()
    {
        return $this->name === 'admin';
    }

    public function isKasir()
    {
        return $this->name === 'kasir';
    }

    public function isGudang()
    {
        return $this->name === 'gudang';
    }
}
