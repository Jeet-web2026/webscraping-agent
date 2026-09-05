<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchRequest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['result' => 'array', 'filters' => 'array'];
    }
}
