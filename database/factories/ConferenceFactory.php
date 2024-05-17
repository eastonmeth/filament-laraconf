<?php

namespace Database\Factories;

use App\Enums\Region;
use App\Models\Conference;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conference::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = new Carbon($this->faker->dateTimeBetween('-1 year', '+1 year'));
        $endDate = $startDate->copy()->addDays($this->faker->numberBetween(1, 4));

        return [
            'name' => ucwords($this->faker->words(3, true)).' '.$startDate->year,
            'description' => $this->faker->text(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement([
                'draft',
                'published',
                'archived',
            ]),
            'region' => $this->faker->randomElement(Region::class),
            'venue_id' => null,
        ];
    }
}
