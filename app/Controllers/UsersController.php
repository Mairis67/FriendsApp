<?php

namespace App\Controllers;

use App\Database;
use App\Models\User;
use App\Models\UserProfile;
use App\Redirect;
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
                $userData['id']
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

        $user = new UserProfile(
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

    public function showRegister(): View
    {
        return new View('Users/register');
    }

    public function register(): Redirect
    {
        Database::connection()
            ->insert('users', [
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ]);

        $user = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('email = ?')
            ->setParameter(0, $_POST['email'])
            ->executeQuery()
            ->fetchAllAssociative();

        Database::connection()
            ->insert('user_profiles', [
                'user_id' => (int) $user['id'],
                'name' => $_POST['name'],
                'surname' => $_POST['surname'],
                'birthday' => $_POST['birthday']
            ]);

        return new Redirect('/users');
    }
}

