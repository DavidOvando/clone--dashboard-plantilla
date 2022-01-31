<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserPersonalInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document',
        'name',
        'last_name',
        'phone',
        'sexo',
        'birth_date',
        'city',
        'observations',
        'personal_email',
        'photo',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
