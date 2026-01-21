<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_index_screen_can_be_rendered()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('clients.index'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Clients/Index')
                ->has('clients.data')
            );
    }

    public function test_client_can_be_created()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('clients.store'), [
            'type' => 'client',
            'full_name' => 'John Doe',
            'phone' => '123456789',
            'email' => 'john@example.com',
            'national_id' => '123456',
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_client_can_be_updated()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->put(route('clients.update', $client), [
            'type' => 'lead',
            'full_name' => 'Jane Doe',
            'phone' => '987654321',
            'email' => 'jane@example.com',
            'national_id' => '654321',
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'full_name' => 'Jane Doe',
            'type' => 'lead',
        ]);
    }

    public function test_client_can_be_deleted()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));
        $this->assertSoftDeleted('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_search_works()
    {
        $user = User::factory()->create();
        Client::factory()->create(['full_name' => 'John Doe']);
        Client::factory()->create(['full_name' => 'Jane Doe']);

        $response = $this->actingAs($user)->get(route('clients.index', ['search' => 'John']));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Clients/Index')
            ->has('clients.data', 1)
            ->where('clients.data.0.full_name', 'John Doe')
        );
    }
}
