<?php

namespace App\Models;

use App\Enums\CommentType;
use App\Models\Scopes\TenantScope;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

#[Fillable([
    'ticket_id',
    'user_id',
    'organization_id',
    'body',
    'type',
    'parent_id',
])]
class Comment extends Model
{
    /** @use HasFactory\u003cCommentFactory\u003e */
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    protected function casts(): array
    {
        return [
            'type' => CommentType::class,
        ];
    }

    /**
     * Temporarily disable tenant scoping for cross-tenant operations (e.g. seeding).
     */
    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope(TenantScope::class);
    }
}
