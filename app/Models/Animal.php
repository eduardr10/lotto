<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
    ];

    protected $cast = [
        'number' => 'integer',
        'name' => 'string',
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

    public function lotteries(): HasMany
    {
        return $this->hasMany(Lottery::class);
    }

    public function scopeInRange($query, array $dates)
    {
        return $query->whereBetween('created_at', $dates);
    }
}
