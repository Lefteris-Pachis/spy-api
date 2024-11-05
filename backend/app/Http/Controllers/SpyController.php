<?php

namespace App\Http\Controllers;

use App\Domain\ValueObjects\Agency;
use App\Domain\ValueObjects\DateOfBirth;
use App\Domain\ValueObjects\DateOfDeath;
use App\Domain\ValueObjects\Name;
use App\Models\Spy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SpyController extends Controller
{
    public function store(Request $request)
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
            // Define validation rules using the model's validation method
            $validator = Validator::make($request->all(), Spy::validationRules());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Create the spy
            $spy = Spy::createSpy($name, $agency, $validatedData['country_of_operation'], $dateOfBirth, $dateOfDeath);

            // Return a successful response
            return response()->json(['message' => 'Spy created successfully.', 'spy' => $spy], 201);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
