<?php

namespace Tests\Feature;

use App\Models\Spy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class SpyControllerTest extends TestCase
{
    use RefreshDatabase; // Automatically reset the database after each test

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_store_a_spy()
    {
        // Create a user for testing
        $user = User::factory()->create();

        $data = [
            'name' => 'James',
            'surname' => 'Bond',
            'agency' => 'MI6',
            'country_of_operation' => 'United Kingdom',
            'date_of_birth' => '1920-04-13',
            'date_of_death' => null,
        ];

        $response = $this->actingAs($user)->postJson('/api/spies', $data);

        $response->assertStatus(JsonResponse::HTTP_CREATED)
            ->assertJson([
                'message' => 'Spy created successfully.',
                'spy' => $data
            ]);

        $this->assertDatabaseHas('spies', $data);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_store_a_spy_without_required_fields()
    {
        $user = User::factory()->create();

        // Missing name and surname
        $response = $this->actingAs($user)->postJson('/api/spies', []);

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name', 'surname']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_store_a_spy_with_invalid_date_format()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'James',
            'surname' => 'Bond',
            'agency' => 'MI6',
            'country_of_operation' => 'United Kingdom',
            'date_of_birth' => 'invalid-date-format',
            'date_of_death' => null,
        ];

        $response = $this->actingAs($user)->postJson('/api/spies', $data);

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['date_of_birth']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_store_a_spy_with_future_date_of_birth()
    {
        $user = User::factory()->create();

        // Generating a future date for date_of_death
        $futureDate = now()->addDays(10)->toDateString(); // 10 days in the future

        // Future date for date_of_birth
        $data = [
            'name' => 'James',
            'surname' => 'Bond',
            'agency' => 'MI6',
            'country_of_operation' => 'United Kingdom',
            'date_of_birth' => $futureDate,
            'date_of_death' => null,
        ];

        $response = $this->actingAs($user)->postJson('/api/spies', $data);

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['date_of_birth']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_store_a_spy_with_future_date_of_death()
    {
        $user = User::factory()->create();

        // Generating a future date for date_of_death
        $futureDate = now()->addDays(10)->toDateString(); // 10 days in the future

        // Future date for date_of_death
        $data = [
            'name' => 'James',
            'surname' => 'Bond',
            'agency' => 'MI6',
            'country_of_operation' => 'United Kingdom',
            'date_of_birth' => '1920-04-13',
            'date_of_death' => $futureDate,
        ];

        $response = $this->actingAs($user)->postJson('/api/spies', $data);

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['date_of_death']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_store_a_spy_without_authentication()
    {
        $data = [
            'name' => 'James',
            'surname' => 'Bond',
            'agency' => 'MI6',
            'country_of_operation' => 'United Kingdom',
            'date_of_birth' => '1920-04-13',
            'date_of_death' => null,
        ];

        $response = $this->postJson('/api/spies', $data);

        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_store_a_spy_with_invalid_agency_name()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'James',
            'surname' => 'Bond',
            'agency' => '', // Agency name is empty.
            'country_of_operation' => 'United Kingdom',
            'date_of_birth' => '1920-04-13',
            'date_of_death' => null,
        ];

        $response = $this->actingAs($user)->postJson('/api/spies', $data);

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['agency']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_store_a_spy_with_invalid_country_of_operation()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'James',
            'surname' => 'Bond',
            'agency' => 'MI6',
            'country_of_operation' => '',  // Empty country
            'date_of_birth' => '1920-04-13',
            'date_of_death' => null,
        ];

        $response = $this->actingAs($user)->postJson('/api/spies', $data);

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['country_of_operation']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_get_random_spies()
    {
        // Create an authenticated user
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);

        // Create 10 spies
        Spy::factory()->count(10)->create();

        // Make the request to get random spies
        $response = $this->getJson('/api/spies/random');

        // Check the status code and structure of the returned JSON
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                '*' => ['name', 'surname', 'agency', 'country_of_operation', 'date_of_birth', 'date_of_death']
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_array_when_no_spies_exist()
    {
        $user = User::factory()->create();  // Create an authenticated user
        $this->actingAs($user);              // Authenticate the user

        // Call the endpoint to get random spies when none exist
        $response = $this->getJson('/api/spies/random');

        // Assert that a successful response is returned and that it is an empty array
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_get_unique_random_spies()
    {
        $user = User::factory()->create(); // Authenticate the user
        $this->actingAs($user);

        Spy::factory()->count(10)->create(); // Create 10 spies

        $response1 = $this->getJson('/api/spies/random');
        $response2 = $this->getJson('/api/spies/random');

        // Ensure the responses are not identical (can vary based on deletions or randomness)
        $this->assertNotEquals($response1->json(), $response2->json());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_get_spies_if_unauthenticated()
    {
        $response = $this->getJson('/api/spies/random');

        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_list_spies_without_authentication()
    {
        $response = $this->getJson('/api/spies');

        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }
}
