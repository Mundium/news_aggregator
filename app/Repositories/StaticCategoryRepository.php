<?php

namespace App\Repositories;

class StaticCategoryRepository implements CategoryRepository
{
    public function getCategories(): array
    {
        return [
            ['id' => 'books', 'name' => 'Books'],
            ['id' => 'business', 'name' => 'Business'],
            ['id' => 'entertainment', 'name' => 'Entertainment'],
            ['id' => 'environment', 'name' => 'Environment'],
            ['id' => 'film', 'name' => 'Film'],
            ['id' => 'food', 'name' => 'Food'],
            ['id' => 'general', 'name' => 'General'],
            ['id' => 'health', 'name' => 'Health'],
            ['id' => 'lifeandstyle', 'name' => 'Life and style'],
            ['id' => 'media', 'name' => 'Media'],
            ['id' => 'music', 'name' => 'Music'],
            ['id' => 'science', 'name' => 'Science'],
            ['id' => 'sport', 'name' => 'Sport'],
            ['id' => 'sports', 'name' => 'Sports'],
            ['id' => 'technology', 'name' => 'Technology'],
            ['id' => 'tv-and-radio', 'name' => 'Television & radio'],
            ['id' => 'world', 'name' => 'World news'],
        ];
    }
}
