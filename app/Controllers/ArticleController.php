<?php

namespace App\Controllers;

use App\Database;
use App\Exceptions\FormValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Article;
use App\Redirect;
use App\Validation\ArticleFormValidator;
use App\Validation\Errors;
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

        // make select query for article likes
        $articleLikes = Database::connection()
            ->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('article_likes')
            ->where('article_id = ?')
            ->setParameter(0, (int) $vars['id'])
            ->executeQuery()
            ->fetchOne();


        return new View('Articles/show', [
            'article' => $article,
            'articleLikes' => (int) $articleLikes
        ]);
    }

    public function store(): Redirect
    {
        // Validate form
        $validator = null;

        try {
            $validator = (new ArticleFormValidator($_POST, [
                'title' => ['required', 'min:3'],
                'description' => ['required']
            ]));
            $validator->passes();
        } catch (FormValidationException $exception) {

            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;

            return new Redirect('/articles/create');
        }

        Database::connection()
            ->insert('articles', [
                'title' => $_POST['title'],
                'description' => $_POST['description']
            ]);

        return new Redirect('/articles');
    }

    public function delete(array $vars): Redirect
    {
        Database::connection()->delete('articles', ['id' => (int) $vars['id']]);

        return new Redirect('/articles');
    }

    public function edit(array $vars): View
    {
        try {
            $articleQuery = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('articles')
                ->where('id = ?')
                ->setParameter(0, (int)$vars['id'])
                ->executeQuery()
                ->fetchAssociative();

            if(!$articleQuery) {
                throw new ResourceNotFoundException('Article with id: ' . $vars['id'] . ' not found');
            }

            $article = new Article(
                $articleQuery['title'],
                $articleQuery['description'],
                $articleQuery['created_at'],
                $articleQuery['id']
            );

            return new View('Articles/edit', [
                'article' => $article
            ]);
        } catch (ResourceNotFoundException $exception) {
            return new View('404');
        }
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

    public function like(array $vars): Redirect
    {
        // Make select query, check if user already liked
        $articleId = (int) $vars['id'];
        Database::connection()->insert('article_likes', [
            'article_id' => $articleId,
            'user_id' => 1 // $_SESSION
        ]);

        return new Redirect('/articles/' . $articleId);
    }
}