<?php

namespace App\Http\Controllers;

use App\Interfaces\ApiTokenRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ApiTokenController extends Controller
{
    public function __construct(protected ApiTokenRepositoryInterface $tokens) {}

    public function index(): JsonResponse
    {
        return response()->json($this->tokens->all());
    }
}
