<?php

namespace App\Helpers;

use App\Interfaces\ApiTokenRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;

class SerpApiHelper
{
    protected static string $baseUrl = 'https://serpapi.com/search.json';

    public static function search(array $query, ApiTokenRepositoryInterface $apiTokenInterface)
    {
        $query['api_key'] = $apiTokenInterface->findById(1)->token;

        $response = Http::timeout(60)
            ->retry(3, 2000)->get(static::$baseUrl, $query);

        return static::handleResponse($response);
    }

    protected static function handleResponse(Response $response)
    {
        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response ?? [];
    }
}
