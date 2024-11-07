<?php

namespace App\Http\Controllers;

use App\Domain\ValueObjects\Agency;
use App\Domain\ValueObjects\DateOfBirth;
use App\Domain\ValueObjects\DateOfDeath;
use App\Domain\ValueObjects\Name;
use App\Models\Spy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SpyController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Validate incoming request
        $validatedData = $request->validate(Spy::validationRules());

        // Create necessary value objects
        $name = new Name($validatedData['name'], $validatedData['surname']);
        $agency = new Agency($validatedData['agency']);
        $dateOfBirth = new DateOfBirth($validatedData['date_of_birth']);
        $dateOfDeath = isset($validatedData['date_of_death'])
            ? new DateOfDeath($validatedData['date_of_death'])
            : null;

        try {
            $spy = Spy::createSpy($name, $agency, $validatedData['country_of_operation'], $dateOfBirth, $dateOfDeath);

            // Return a successful response
            return response()->json(['message' => 'Spy created successfully.', 'spy' => $spy], 201);
        } catch (\Exception $e) {
            // Handle validation exceptions separately
            if ($e instanceof ValidationException) {
                return response()->json(['errors' => $e->validator->errors()], 422);
            }
            // Handle general exceptions
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getRandomSpies(Request $request): JsonResponse
    {
        // Fetch 5 random spies
        $spies = Spy::inRandomOrder()->limit(5)->get();

        // Return the spies as a JSON response
        return response()->json($spies);
    }

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'age_min' => 'nullable|integer|min:0',
            'age_max' => 'nullable|integer|min:0|gte:age_min',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $query = Spy::query();

        // Handle filtering by name or surname
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('surname')) {
            $query->where('surname', 'like', '%' . $request->input('surname') . '%');
        }

        // Handle filtering by exact age
        if ($request->filled('age_exact')) {
            $age = (int) $request->input('age_exact');
            $startDate = now()->subYears($age)->startOfDay();
            $endDate = now()->subYears($age + 1)->endOfDay();
            $query->whereBetween('date_of_birth', [$endDate, $startDate]);
        }

        // Handle filtering by age range
        if ($request->filled('age_min') || $request->filled('age_max')) {
            $ageMin = $request->input('age_min') ? (int) $request->input('age_min') : null;
            $ageMax = $request->input('age_max') ? (int) $request->input('age_max') : null;

            // Initialize start and end dates to null
            $startDate = null;
            $endDate = null;

            // If both age limits are provided
            if ($ageMin !== null && $ageMax !== null) {
                // Start date is the day when someone will turn ageMax (older boundary)
                $startDate = now()->subYears($ageMax)->startOfDay();
                // End date is the end of the day when someone will turn ageMin (younger boundary)
                $endDate = now()->subYears($ageMin)->endOfDay();
                $query->whereBetween('date_of_birth', [$startDate, $endDate]);
            } elseif ($ageMin !== null) {
                // Filter for minimum age
                $startDate = now()->subYears($ageMin)->startOfDay();
                // Anyone born on or before the start date qualifies
                $query->where('date_of_birth', '<=', $startDate);
            } elseif ($ageMax !== null) {
                // Filter for maximum age
                $endDate = now()->subYears($ageMax)->endOfDay();
                // Anyone born on or after the end date qualifies
                $query->where('date_of_birth', '>=', $endDate);
            }
        }

        // Handle sorting
        if ($request->filled('sort_by')) {
            $sortFields = explode(',', $request->input('sort_by'));
            foreach ($sortFields as $field) {
                if (in_array(trim($field), ['name', 'surname', 'date_of_birth', 'date_of_death'])) {
                    $query->orderBy(trim($field));
                } else {
                    return response()->json(['error' => 'Unsupported sort field: ' . trim($field)], 422);
                }
            }
        }

        // Validate pagination input
        $perPage = $request->query('per_page', 10);
        if (!is_numeric($perPage) || $perPage <= 0) {
            return response()->json(['error' => 'Invalid per_page parameter.'], 422);
        }

        // Paginate results
        $spies = $query->paginate($perPage);

        return response()->json($spies);
    }
}
