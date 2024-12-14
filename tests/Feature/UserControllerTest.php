<?php


namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

//    index

    public function test_admin_can_list_users()
    {
        Passport::actingAs($this->admin);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'users' => [['id', 'name', 'email', 'role']],
            ]);
    }

    public function test_non_admin_cannot_list_users()
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/users');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized to view all users',
            ]);
    }

    public function test_unauthenticated_user_cannot_list_users()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }


//show
    public function test_admin_can_view_any_user()
    {
        Passport::actingAs($this->admin);

        $response = $this->getJson('/api/users/' . $this->user->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_user_can_view_own_profile()
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/users/' . $this->user->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully retrieved user',
            ]);
    }

    public function test_user_cannot_view_other_profiles()
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/users/' . $this->admin->id);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized to view this user',
            ]);
    }
//delte
    public function test_admin_can_delete_user()
    {
        Passport::actingAs($this->admin);

        $response = $this->deleteJson('/api/users/' . $this->user->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User account deleted successfully',
            ]);

        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    public function test_user_cannot_delete_other_users()
    {
        Passport::actingAs($this->user);

        $response = $this->deleteJson('/api/users/' . $this->admin->id);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized to delete this account',
            ]);
    }


//    update
    public function test_user_can_update_own_profile()
    {
        Passport::actingAs($this->user);

        $response = $this->putJson('/api/users/' . $this->user->id, [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User account updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_admin_can_update_any_user()
    {
        Passport::actingAs($this->admin);

        $response = $this->putJson('/api/users/' . $this->user->id, [
            'name' => 'Admin Updated Name',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User account updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Admin Updated Name',
        ]);
    }

    public function test_validation_fails_on_invalid_email()
    {
        Passport::actingAs($this->user);

        $response = $this->putJson('/api/users/' . $this->user->id, [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'message', 'errors' => ['email']]);
    }


}
