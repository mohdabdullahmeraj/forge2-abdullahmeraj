<?php

namespace Database\Factories;

use App\Enums\CommentType;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory\u003cComment\u003e
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'organization_id' => null,
            'body' => fake()->paragraph(),
            'type' => fake()->randomElement(array_column(CommentType::cases(), 'value')),
            'parent_id' => null,
        ];
    }
}
