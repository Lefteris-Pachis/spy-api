<?php

namespace App\Console\Commands;

use App\Domain\ValueObjects\Agency;
use App\Domain\ValueObjects\DateOfBirth;
use App\Domain\ValueObjects\DateOfDeath;
use App\Domain\ValueObjects\Name;
use App\Models\Spy;
use Illuminate\Console\Command;

class CreateSpyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spy:create
                            {name : The name of the spy}
                            {surname : The surname of the spy}
                            {agency : The agency of the spy (CIA, MI6, KGB)}
                            {country : The country of operation}
                            {dob : The date of birth (YYYY-MM-DD)}
                            {dod? : The date of death (YYYY-MM-DD), if applicable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new spy record';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = new Name($this->argument('name'), $this->argument('surname'));
        $agency = new Agency($this->argument('agency'));
        $country_of_operation = $this->argument('country');

        $date_of_birth_string = $this->argument('dob');
        $date_of_birth = new DateOfBirth($date_of_birth_string);

        $date_of_death = null;
        if ($this->argument('dod')) {
            $date_of_death_string = $this->argument('dod');
            $date_of_death = new DateOfDeath($date_of_death_string);
        }

        try {
            $spy = Spy::createSpy($name, $agency, $country_of_operation, $date_of_birth, $date_of_death);
            $this->info('Spy created successfully: ' . $spy->id);
        } catch (\InvalidArgumentException $e) {
            $this->error('Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
