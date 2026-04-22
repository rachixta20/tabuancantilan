<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    protected $fillable = ['admin_id', 'action', 'subject_type', 'subject_id', 'before', 'after', 'notes'];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }

    public static function record(string $action, Model $subject, array $before = [], array $after = [], ?string $notes = null): void
    {
        static::create([
            'admin_id'     => auth()->id(),
            'action'       => $action,
            'subject_type' => class_basename($subject),
            'subject_id'   => $subject->getKey(),
            'before'       => $before ?: null,
            'after'        => $after ?: null,
            'notes'        => $notes,
        ]);
    }
}
