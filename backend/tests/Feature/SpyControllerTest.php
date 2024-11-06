<?php

namespace Tests\Feature;

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
}
