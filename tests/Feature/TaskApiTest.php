<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $response->json('token');

        return [$user, $token];
    }

    public function test_create_task()
    {
        [$user, $token] = $this->authenticate();

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/tasks', [
            'title' => 'Test Task',
            'body' => 'Test body of task'
        ]);

        $response->assertStatus(201)->assertJsonFragment(['title' => 'Test Task']);
    }

    public function test_update_task()
    {
        [$user, $token] = $this->authenticate();

        $task = $user->tasks()->create([
            'title' => 'Old title',
            'body' => 'Old body',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")->putJson("/api/tasks/{$task->id}", [
            'title' => 'New title',
            'body' => 'New body'
        ]);

        $response->assertStatus(200)->assertJsonFragment(['title' => 'New title']);
    }

    public function test_delete_task()
    {
        [$user, $token] = $this->authenticate();

        $task = $user->tasks()->create([
            'title' => 'To be deleted',
            'body' => 'Delete this one',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)->assertJson(['message' => 'Task deleted']);
    }

    public function test_mark_as_completed()
    {
        [$user, $token] = $this->authenticate();

        $task = $user->tasks()->create([
            'title' => 'Incomplete',
            'body' => 'Still working on it',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")->patchJson("/api/tasks/{$task->id}/complete");

        $response->assertStatus(200)->assertJson(['message' => 'Task marked as completed']);
    }
}
