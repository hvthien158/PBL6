<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Systemtime extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'id',
        '_date',
        'time_check_in',
        'time_check_out',
    ];
}
