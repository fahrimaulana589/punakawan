<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pegawai>
 */
class PegawaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nama" => $this->faker->name(),
            "jabatan" => "pegawai",
            "alamat" => $this->faker->address(),
            "no_hp" => $this->faker->phoneNumber(),
            "jenis_kelamin" => $this->faker->randomElement(['L', 'P']),
            "gaji" => $this->faker->numberBetween(3000000, 10000000),
        ];
    }
}
