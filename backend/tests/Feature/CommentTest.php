<?php

namespace Tests\Feature;

use App\Enums\CommentType;
use App\Enums\Role;
use App\Models\Comment;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private Organization $org;
    private User $admin;
    private User $agent;
    private User $customer;
    private Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = Organization::create(['name' => 'Test Org', 'slug' => 'test-org']);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.test',
            'password' => Hash::make('password'),
            'organization_id' => $this->org->id,
            'role' => Role::Admin->value,
        ]);

        $this->agent = User::create([
            'name' => 'Agent',
            'email' => 'agent@test.test',
            'password' => Hash::make('password'),
            'organization_id' => $this->org->id,
            'role' => Role::Agent->value,
        ]);

        $this->customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@test.test',
            'password' => Hash::make('password'),
            'organization_id' => $this->org->id,
            'role' => Role::Customer->value,
        ]);

        $this->ticket = Ticket::create([
            'organization_id' => $this->org->id,
            'requester_id' => $this->customer->id,
            'subject' => 'Test Ticket',
            'description' => 'Test description',
            'status' => 'open',
            'priority' => 'medium',
        ]);
    }

    public function test_agent_sees_all_comments(): void
    {
        Comment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->customer->id,
            'organization_id' => $this->org->id,
            'body' => 'Public comment',
            'type' => CommentType::Public->value,
        ]);

        Comment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->agent->id,
            'organization_id' => $this->org->id,
            'body' => 'Internal note',
            'type' => CommentType::Internal->value,
        ]);

        $token = $this->agent->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/tickets/{$this->ticket->id}/comments");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    public function test_customer_sees_only_public_comments(): void
    {
        Comment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->customer->id,
            'organization_id' => $this->org->id,
            'body' => 'Public comment',
            'type' => CommentType::Public->value,
        ]);

        Comment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->agent->id,
            'organization_id' => $this->org->id,
            'body' => 'Internal note',
            'type' => CommentType::Internal->value,
        ]);

        $token = $this->customer->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/tickets/{$this->ticket->id}/comments");

        $response->assertStatus(200);
        $comments = $response->json();
        $this->assertCount(1, $comments);
        $this->assertEquals('public', $comments[0]['type']);
    }

    public function test_customer_cannot_post_internal_comment(): void
    {
        $token = $this->customer->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/tickets/{$this->ticket->id}/comments", [
                'body' => 'Internal comment',
                'type' => 'internal',
            ]);

        $response->assertStatus(403);
    }

    public function test_agent_can_post_internal_comment(): void
    {
        $token = $this->agent->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/tickets/{$this->ticket->id}/comments", [
                'body' => 'Internal note',
                'type' => 'internal',
            ]);

        $response->assertStatus(201);
        $this->assertEquals('internal', $response->json('type'));
    }

    public function test_threading_works(): void
    {
        $parent = Comment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->customer->id,
            'organization_id' => $this->org->id,
            'body' => 'Parent comment',
            'type' => CommentType::Public->value,
        ]);

        $token = $this->agent->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/tickets/{$this->ticket->id}/comments", [
                'body' => 'Reply comment',
                'parent_id' => $parent->id,
            ]);

        $response->assertStatus(201);
        $this->assertEquals($parent->id, $response->json('parent_id'));
    }

    public function test_comments_are_tenant_scoped(): void
    {
        $otherOrg = Organization::create(['name' => 'Other Org', 'slug' => 'other-org']);
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@test.test',
            'password' => Hash::make('password'),
            'organization_id' => $otherOrg->id,
            'role' => Role::Admin->value,
        ]);

        $otherTicket = Ticket::create([
            'organization_id' => $otherOrg->id,
            'requester_id' => $otherUser->id,
            'subject' => 'Other Ticket',
            'description' => 'Other description',
            'status' => 'open',
            'priority' => 'medium',
        ]);

        Comment::create([
            'ticket_id' => $otherTicket->id,
            'user_id' => $otherUser->id,
            'organization_id' => $otherOrg->id,
            'body' => 'Other comment',
            'type' => CommentType::Public->value,
        ]);

        $token = $this->agent->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/tickets/{$this->ticket->id}/comments");

        $response->assertStatus(200);
        $comments = $response->json();
        $this->assertCount(0, $comments);
    }
}
