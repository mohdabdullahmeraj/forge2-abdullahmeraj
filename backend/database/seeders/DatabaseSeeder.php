<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\CommentType;
use App\Models\Comment;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Acme organization
        $acme = Organization::create([
            'name' => 'Acme',
            'slug' => 'acme',
        ]);

        $acmeAdmin = User::create([
            'name' => 'Acme Admin',
            'email' => 'admin@acme.test',
            'password' => Hash::make('password'),
            'organization_id' => $acme->id,
            'role' => Role::Admin->value,
        ]);

        $acmeAgent = User::create([
            'name' => 'Acme Agent',
            'email' => 'agent@acme.test',
            'password' => Hash::make('password'),
            'organization_id' => $acme->id,
            'role' => Role::Agent->value,
        ]);

        $acmeCustomer = User::create([
            'name' => 'Acme Customer',
            'email' => 'customer@acme.test',
            'password' => Hash::make('password'),
            'organization_id' => $acme->id,
            'role' => Role::Customer->value,
        ]);

        // Globex organization
        $globex = Organization::create([
            'name' => 'Globex',
            'slug' => 'globex',
        ]);

        $globexAdmin = User::create([
            'name' => 'Globex Admin',
            'email' => 'admin@globex.test',
            'password' => Hash::make('password'),
            'organization_id' => $globex->id,
            'role' => Role::Admin->value,
        ]);

        $globexAgent = User::create([
            'name' => 'Globex Agent',
            'email' => 'agent@globex.test',
            'password' => Hash::make('password'),
            'organization_id' => $globex->id,
            'role' => Role::Agent->value,
        ]);

        $globexCustomer = User::create([
            'name' => 'Globex Customer',
            'email' => 'customer@globex.test',
            'password' => Hash::make('password'),
            'organization_id' => $globex->id,
            'role' => Role::Customer->value,
        ]);

        // 8 tickets for Acme
        Ticket::factory()->count(8)->create([
            'organization_id' => $acme->id,
            'requester_id' => $acmeCustomer->id,
        ]);

        // 4 tickets for Globex
        Ticket::factory()->count(4)->create([
            'organization_id' => $globex->id,
            'requester_id' => $globexCustomer->id,
        ]);

        // 2-3 comments per ticket (mix public/internal + threaded reply)
        $allTickets = Ticket::all();
        foreach ($allTickets as $ticket) {
            $orgUsers = User::where('organization_id', $ticket->organization_id)->get();
            $customer = $orgUsers->firstWhere('role', Role::Customer->value);
            $agent = $orgUsers->firstWhere('role', Role::Agent->value);

            // Public comment from customer
            $parentComment = Comment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $customer->id,
                'organization_id' => $ticket->organization_id,
                'body' => fake()->paragraph(),
                'type' => CommentType::Public->value,
            ]);

            // Internal comment from agent
            Comment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $agent->id,
                'organization_id' => $ticket->organization_id,
                'body' => fake()->paragraph(),
                'type' => CommentType::Internal->value,
            ]);

            // Threaded reply to public comment
            Comment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $agent->id,
                'organization_id' => $ticket->organization_id,
                'body' => fake()->paragraph(),
                'type' => CommentType::Public->value,
                'parent_id' => $parentComment->id,
            ]);
        }
    }
}
