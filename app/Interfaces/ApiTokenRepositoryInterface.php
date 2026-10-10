<?php

namespace App\Interfaces;

use App\Models\ApiToken;
use Illuminate\Database\Eloquent\Collection;

interface ApiTokenRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?ApiToken;

    public function create(string $name, string $token): array;

    public function update(int $id, string $name, string $token): ?ApiToken;

    public function regenerate(int $id): ?array;

    public function delete(int $id): bool;
}
