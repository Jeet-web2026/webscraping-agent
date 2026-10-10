<?php

namespace Database\Seeders;

use App\Models\ApiToken;
use Illuminate\Database\Seeder;

class ApiTokenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                "name" => "SerpApi",
                "token" => "2453813d3e3d9f2f2cf8f9a295c1b50a5f23eb6b997924ffadae65c377155f28",
                "token_source" => "https://serpapi.com/dashboard"
            ],
            [
                "name" => "Scrape do",
                "token" => "75630e9cfca24adf8540741f0d94e5b9362ce5a4264",
                "token_source" => "https://dashboard.scrape.do/login"
            ]
        ];

        foreach ($data as $item) {
            ApiToken::updateOrCreate(
                ['name' => $item['name']],
                ['token' => $item['token'], 'token_source' => $item['token_source']]
            );

            $this->command->info("{$item['name']}: {$item['token']}");
        }
    }
}
