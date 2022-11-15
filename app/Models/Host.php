<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Host extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $cast = [
        'name' => 'string',
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function loteries(): HasMany
    {
        return $this->hasMany(Lottery::class);
    }
}
