<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        $articles = [
            [
                'title' => 'Welcome to Laravel',
                'text' => 'This is an introductory article about Laravel framework.',
                'author' => 'kobes'
            ],
            [
                'title' => 'Using Spatie Permissions',
                'text' => 'Learn how to manage roles and permissions with Spatie.',
                'author' => 'durant'
            ],
            [
                'title' => 'Why Sanctum for APIs',
                'text' => 'An overview of Laravel Sanctum and token-based auth.',
                'author' => 'james'
            ]
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }
    }
}
