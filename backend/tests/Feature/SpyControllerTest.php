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

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_list_paginated_spies()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Spy::factory()->count(30)->create(); // Creates 30 spies

        $response = $this->getJson('/api/spies?page=1&per_page=10');

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'surname',
                    'agency',
                    'country_of_operation',
                    'date_of_birth',
                    'date_of_death',
                    'created_at',
                    'updated_at',
                ]],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_spies_by_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $spy1 = Spy::factory()->create(['name' => 'James', 'surname' => 'Bond']);
        Spy::factory()->create(['name' => 'John', 'surname' => 'Doe']);

        $response = $this->getJson('/api/spies?name=James&page=1&per_page=10');

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonFragment(['name' => $spy1->name, 'surname' => $spy1->surname]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_spies_by_surname()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $spy1 = Spy::factory()->create(['name' => 'James', 'surname' => 'Bond']);
        Spy::factory()->create(['name' => 'John', 'surname' => 'Doe']);

        $response = $this->getJson('/api/spies?surname=Bond&page=1&per_page=10');

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonFragment(['name' => $spy1->name, 'surname' => $spy1->surname]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_spies_by_exact_age()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Spy::factory()->create(['date_of_birth' => now()->subYears(30)->toDateString()]);
        Spy::factory()->create(['date_of_birth' => now()->subYears(35)->toDateString()]);

        $response = $this->getJson('/api/spies?age_exact=30&page=1&per_page=10');

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonCount(1, 'data');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_spies_by_age_range()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create spies aged 30 and 31
        Spy::factory()->create(['date_of_birth' => now()->subYears(30)->toDateString()]); // 30 years old
        Spy::factory()->create(['date_of_birth' => now()->subYears(31)->toDateString()]); // 31 years old
        // Create spies that are not in the specified age range
        Spy::factory()->create(['date_of_birth' => now()->subYears(40)->toDateString()]); // 40 years old
        Spy::factory()->create(['date_of_birth' => now()->subYears(29)->toDateString()]); // 29 years old

        $response = $this->getJson('/api/spies?age_min=30&age_max=35&page=1&per_page=10');

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonCount(2, 'data'); // Expecting 2 spies: 30 and 31 years old
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_sort_spies_by_name_and_surname()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Spy::factory()->create(['name' => 'Alice', 'surname' => 'Smith']);
        Spy::factory()->create(['name' => 'Bob', 'surname' => 'Zeta']);
        Spy::factory()->create(['name' => 'Alice', 'surname' => 'Adams']);

        $response = $this->getJson('/api/spies?sort_by=name,surname&page=1&per_page=10');

        $response->assertStatus(JsonResponse::HTTP_OK);

        // Verify the order of names in the response
        $spies = $response->json('data');
        $this->assertEquals('Alice', $spies[0]['name']);
        $this->assertEquals('Adams', $spies[0]['surname']);
        $this->assertEquals('Alice', $spies[1]['name']);
        $this->assertEquals('Smith', $spies[1]['surname']);
        $this->assertEquals('Bob', $spies[2]['name']);
        $this->assertEquals('Zeta', $spies[2]['surname']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_for_unsupported_sort_field()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/spies?sort_by=unsupported_field&page=1&per_page=10');

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['error' => 'Unsupported sort field: unsupported_field']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_for_invalid_per_page_parameter()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/spies?per_page=-1');

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['error' => 'Invalid per_page parameter.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_for_invalid_age_filter()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/spies?age_min=not_an_integer&page=1&per_page=10');

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['error']);
    }
}
