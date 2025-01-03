<?php

namespace App\Models;

use App\Domain\ValueObjects\Agency;
use App\Domain\ValueObjects\DateOfBirth;
use App\Domain\ValueObjects\DateOfDeath;
use App\Domain\ValueObjects\Name;
use App\Events\SpyCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Spy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'agency',
        'country_of_operation',
        'date_of_birth',
        'date_of_death',
    ];

    // Validation rules for creating/updating a spy
    public static function validationRules(?int $id = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'surname' => [
                'required',
                'string',
                'max:255',
                // Unique validation rule for the combination of name and surname
                Rule::unique('spies')->where(function ($query) use ($id) {
                    if ($id) {
                        $query->where('id', '!=', $id);
                    }
                    return $query;
                }),
            ],
            'agency' => 'required|in:CIA,MI6,KGB',
            'country_of_operation' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'date_of_death' => 'nullable|date|after_or_equal:date_of_birth|before_or_equal:today',
        ];
    }

    // Method to create a spy using value objects
    public static function createSpy(Name $name, Agency $agency, string $country_of_operation, DateOfBirth $date_of_birth, DateOfDeath $date_of_death = null)
    {
        // Prepare input data
        $data = [
            'name' => $name->getValue(),
            'surname' => $name->getSurname(),
            'agency' => $agency->getValue(),
            'country_of_operation' => $country_of_operation,
            'date_of_birth' => $date_of_birth->getValue()->format('Y-m-d'),
            'date_of_death' => $date_of_death ? $date_of_death->getValue()->format('Y-m-d') : null,
        ];

        // Run validation
        $validator = Validator::make($data, self::validationRules());

        // If validation fails, throw an exception
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Create the spy record
        $spy = self::create($data);

        // Dispatch the SpyCreated event
        event(new SpyCreated($spy));

        return $spy;
    }
}
