<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'clients';

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'city',
        'email',
        'phone',
        'address',
        'contact_person',
        'add_by',
        'isActive'
        // Add other fields here
    ];

}
