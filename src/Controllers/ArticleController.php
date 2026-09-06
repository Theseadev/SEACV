<?php
namespace App\Controllers;

use Flight;
use App\Services\ArticleService;

class ArticleController {
    /**
     * Display list of articles and career news
     */
    public function index(): void {
        $request = Flight::request();
        $category = trim($request->query->category ?? $request->query->cat ?? '');
        $search = trim($request->query->search ?? $request->query->q ?? '');

        if (empty($category)) {
            $category = 'Semua';
        }

        $articles = ArticleService::getAll($category, $search);
        $categories = ArticleService::getCategories();

        Flight::render('articles/index', [
            'articles' => $articles,
            'categories' => $categories,
            'selectedCategory' => $category,
            'searchQuery' => $search
        ]);
    }

    /**
     * Display single article details
     */
    public function show(string $slug): void {
        $slug = trim($slug);
        $article = ArticleService::getBySlug($slug);

        if (!$article) {
            Flight::notFound();
            return;
        }

        $relatedArticles = ArticleService::getRelated($slug, 3);

        Flight::render('articles/detail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles
        ]);
    }
}
