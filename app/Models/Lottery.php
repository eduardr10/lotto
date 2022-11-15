<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lottery extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id',
        'host_id'
    ];

    protected $cast = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    public function scopeInRange($query, array $dates)
    {
        return $query->whereBetween('created_at', $dates);
    }

    public function scopeMostAppearances($query, array $dates)
    {
        return $query->whereBetween('created_at', $dates);
    }
}
