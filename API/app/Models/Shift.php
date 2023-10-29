<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use HasFactory;
    protected $table = 'shifts';
    protected $fillable = [
        'name',
        'amount',
        'time_valid_check_in',
        'time_valid_check_out'
    ];
    public function timeKeeping(): HasMany
    {
        return $this->hasMany(Shift::class, 'shift_id');
    }
}
