<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_tasks_with_authorization()
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /** @test */
    public function it_can_store_a_task_with_authorization()
    {
        $data = [
            'name' => 'New Task',
            'scheduled_day' => '2021-01-01',
        ];

        $response = $this->postJson(route('tasks.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonFragment($data);
        $this->assertDatabaseHas('tasks', $data);
    }

    /** @test */
    public function it_can_show_a_task_with_authorization()
    {
        $task = Task::factory()->create();

        $response = $this->getJson(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $task->id]);
    }

    /** @test */
    public function it_can_update_a_task_with_authorization()
    {
        $task = Task::factory()->create();

        $updatedData = [
            'name' => 'Updated Title',
            'scheduled_day' => '2021-01-02',
        ];

        $response = $this->putJson(route('tasks.update', $task), $updatedData);

        $response->assertStatus(200);
        $response->assertJsonFragment($updatedData);
        $this->assertDatabaseHas('tasks', $updatedData);
    }

    /** @test */
    public function it_can_delete_a_task_with_authorization()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson(route('tasks.destroy', $task));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
