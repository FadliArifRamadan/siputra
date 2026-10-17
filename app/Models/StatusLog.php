<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'old_status',
        'new_status',
        'actor_id',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'loggable_id' => 'integer',
            'actor_id' => 'integer',
        ];
    }

    // Polymorphic relation (inverse)
    public function loggable()
    {
        return $this->morphTo();
    }

    // Actor who performed the action
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}