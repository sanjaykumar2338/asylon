<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'organization',
        'organization_type',
        'role',
        'email',
        'phone',
        'meeting',
        'time_window',
        'concerns',
    ];
}
