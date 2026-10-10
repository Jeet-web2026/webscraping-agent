<?php

namespace App\Http\Controllers;

use App\Interfaces\ApiTokenRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function __construct(protected ApiTokenRepositoryInterface $tokens) {}

    public function index(): JsonResponse
    {
        return response()->json($this->tokens->all());
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tokens' => ['required', 'array'],
            'tokens.*.id' => ['required', 'integer', 'exists:api_tokens,id'],
            'tokens.*.name' => ['required', 'string', 'max:255'],
            'tokens.*.token' => ['required', 'string'],
            'tokens.*.token_source' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['tokens'] as $item) {
            $this->tokens->update((int) $item['id'], [
                'name' => $item['name'],
                'token' => $item['token'],
                'token_source' => $item['token_source'] ?? null,
            ]);
        }

        return back()->with('success', 'API tokens updated.');
    }
}
