<?php

namespace App\Repositories;

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Models\ApiToken;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ApiTokenRepository implements ApiTokenRepositoryInterface
{
    public function __construct(protected ApiToken $model) {}

    public function all(): Collection
    {
        return $this->model->newQuery()->latest()->get();
    }

    public function findById(int $id): ?ApiToken
    {
        return $this->model->newQuery()->find($id);
    }

    public function create(string $name, string $token): array
    {
        $model = $this->model->newQuery()->create([
            'name' => $name,
            'token' => $token,
        ]);

        return [
            'model' => $model,
            'plain_text_token' => $token,
        ];
    }

    public function update(int $id, string $name, string $token): ?ApiToken
    {
        $model = $this->findById($id);

        if (! $model) {
            return null;
        }

        $model->update([
            'name' => $name,
            'token' => $token,
        ]);

        return $model;
    }

    public function regenerate(int $id): ?array
    {
        $model = $this->findById($id);

        if (! $model) {
            return null;
        }

        $plainToken = Str::random(40);
        $model->update(['token' => $this->hash($plainToken)]);

        return [
            'model' => $model,
            'plain_text_token' => $plainToken,
        ];
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->newQuery()->whereKey($id)->delete();
    }

    protected function hash(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }
}
