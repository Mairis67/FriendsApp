<?php

namespace App\Controllers;

use App\Database;
use App\Models\UserProfile;
use App\Redirect;
use App\View;

class AuthController
{
    public function home(array $vars): View
    {
        $userQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, (int)$vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        $userProfileQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->where('user_id = ?')
            ->setParameter(0, (int)$vars['id'])
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

        return new View('Users/home', [
            'user' => $user
        ]);
    }

    public function showLogin(): View
    {
        return new View('Users/login');
    }

    public function login(): Redirect
    {
        if(isset($_POST['submit'])) {
            $userQuery = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('users')
                ->where('email = ?')
                ->setParameter(0, $_POST['email'])
                ->executeQuery()
                ->fetchAllAssociative();

            if($userQuery !== null) {
                $user = $userQuery;

                $hashedPassword = $user[0]['password'];
                $checkedPassword = password_verify($_POST['password'], $hashedPassword);

                if(!$checkedPassword) {
                    return new Redirect('/login');
                }

                $userProfileQuery = Database::connection()
                    ->createQueryBuilder()
                    ->select('*')
                    ->from('user_profiles')
                    ->where('user_id = ?')
                    ->setParameter(0, $user[0]['id'])
                    ->executeQuery()
                    ->fetchAllAssociative();

                session_start();
                $_SESSION['userid'] = htmlentities($user[0]['id']);
            } else {
                return new Redirect('/login');
            }
        }
        return new Redirect('users/');
    }
}