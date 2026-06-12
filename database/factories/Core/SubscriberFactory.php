<?php

namespace Database\Factories\Core;

use App\Models\Core\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriberFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = Subscriber::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition(): array
	{
		return [
			'first_name'       => $this->faker->firstName,
			'last_name'        => $this->faker->lastName,
			'email'            => $this->faker->email,
			'password'         => $this->faker->password,
			'accept_condition' => true,
			'email_validated' => true,
			'active'           => true
		];
	}
}
