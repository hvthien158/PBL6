<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $fillable = [
        'department_name',
        'address',
        'email',
        'phone_number',
        'department_manager_id'
    ];
    public $timestamps = false;
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }
    public function manager(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'department_manager_id');
    }
}
