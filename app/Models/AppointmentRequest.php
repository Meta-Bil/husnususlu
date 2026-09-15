<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentRequest extends Model
{
    use HasFactory;

    /**
     * Filled from the public form, so only the visitor's own fields are mass
     * assignable; status and notes are set in the admin panel.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'complaint',
        'preferred_time',
        'preferred_date',
        'message',
        'locale',
        'source_url',
        'ip',
        'user_agent',
        'consented_at',
    ];

    #[Scope]
    protected function unhandled(Builder $query): Builder
    {
        return $query->where('status', AppointmentStatus::New);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => AppointmentStatus::class,
            'preferred_date' => 'date',
            'consented_at' => 'datetime',
            'notified_at' => 'datetime',
        ];
    }
}
