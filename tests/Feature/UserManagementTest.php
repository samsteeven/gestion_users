<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing users.
     */
    public function test_can_list_users(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /**
     * Test create user page.
     */
    public function test_can_see_create_user_page(): void
    {
        $response = $this->get('/users/create');

        $response->assertStatus(200);
        $response->assertSee('Ajouter un Utilisateur');
    }

    /**
     * Test storing a user.
     */
    public function test_can_store_user(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/users', $userData);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test update user page.
     */
    public function test_can_see_edit_user_page(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Modifier');
    }

    /**
     * Test updating a user.
     */
    public function test_can_update_user(): void
    {
        $user = User::factory()->create();
        $updatedData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $response = $this->put("/users/{$user->id}", $updatedData);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }

    /**
     * Test deleting a user.
     */
    public function test_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->delete("/users/{$user->id}");

        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
