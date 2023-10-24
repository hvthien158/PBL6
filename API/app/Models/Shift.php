<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function timeKeeping(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
