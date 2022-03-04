<?php

namespace App\Controllers;

use App\Database;
use App\Models\Article;
use App\Redirect;
use App\View;

class ArticleController
{
    public function index(): View
    {
        $articlesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->orderBy('id', 'desc')
            ->executeQuery()
            ->fetchAllAssociative();

        $articles = [];

        foreach ($articlesQuery as $articleData) {
            $articles [] = new Article(
                $articleData['title'],
                $articleData['description'],
                $articleData['created_at'],
                $articleData['id']
            );
        }

        return new View('Articles/index', [
            'articles' => $articles
        ]);
    }

    public function show(array $vars): View
    {
        $articlesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = ?')
            ->setParameter(0, (int) $vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        $article = new Article(
            $articlesQuery['title'],
            $articlesQuery['description'],
            $articlesQuery['created_at'],
            $articlesQuery['id']
        );



        return new View('Articles/show', [
            'article' => $article
        ]);
    }

    public function create(): View
    {
        return new View('Articles/create');
    }

    public function store(): Redirect
    {
        // Validate form

        Database::connection()
            ->insert('articles', [
                'title' => $_POST['title'],
                'description' => $_POST['description']
            ]);

        // redirect
        return new Redirect('/articles');
    }

    public function delete(array $vars): Redirect
    {
        Database::connection()->delete('articles', ['id' => (int) $vars['id']]);

        return new Redirect('/articles');
    }

    public function edit(array $vars): View
    {
        $articleQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = ?')
            ->setParameter(0, (int) $vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        $article = new Article(
            $articleQuery['title'],
            $articleQuery['description'],
            $articleQuery['created_at'],
            $articleQuery['id']
        );

        return new View('Articles/edit', [
            'article' => $article
        ]);
    }

    public function update(array $vars): Redirect
    {
        // UPDATE articles SET title = ? AND description = ? WHERE id = ?
        Database::connection()->update('articles', [
            'title' => $_POST['title'],
            'description' => $_POST['description']
        ], ['id' => (int) $vars['id']]);

        return new Redirect('/articles/' . $vars['id'] . '/edit'); // articles/10/edit
    }
}