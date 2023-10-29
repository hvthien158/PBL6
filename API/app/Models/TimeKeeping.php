<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Shift;

class TimeKeeping extends Model
{
    use HasFactory;
    protected $table = 'time_keepings';
    protected $fillable = [
        'time_check_in',
        'user_id'
    ];
    public $timestamps = false;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function shift(): BelongsTo
    {
        return $this->BelongsTo(Shift::class, 'shift_id');
    }
}
