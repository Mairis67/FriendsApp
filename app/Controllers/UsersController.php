<?php

namespace App\Controllers;

use App\Database;
use App\Models\User;
use App\View;

class UsersController
{
    public function index(): View
    {
        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->executeQuery()
            ->fetchAllAssociative();

            $users = [];

        foreach ($usersQuery as $userData) {
            $users [] = new User(
                $userData['email'],
                $userData['password'],
                $userData['created_at'],
                $userData['id'],
                $userData['name'],
                $userData['surname'],
                $userData['birthday']
            );
        }

        return new View('Users/index', [
            'users' => $users
        ]);
    }

    public function show(array $vars): View
    {
        $userQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, (int) $vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        $userProfileQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->where('user_id = ?')
            ->setParameter(0, (int) $vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        $user = new User(
            $userProfileQuery['name'],
            $userProfileQuery['surname'],
            $userProfileQuery['birthday'],
            $userQuery['email'],
            $userQuery['password'],
            $userQuery['created_at'],
            $userQuery['id']
        );

        return new View('Users/show', [
            'user' => $user
        ]);
    }
}

