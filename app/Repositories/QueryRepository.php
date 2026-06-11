<?php

namespace App\Repositories;
use App\Models\SearchQuery;
class QueryRepository
{
    public function saveUnique(string $queryText): void
    {
        SearchQuery::firstOrCreate(['query' => $queryText]);
    }
}
