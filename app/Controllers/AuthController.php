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


    /// VISS SLIKTI EL VE
    public function login(): Redirect
    {
        $status = null;

        $user = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('email = ?')
            ->setParameter(0,  $_POST['email'])
            ->executeQuery()
            ->fetchAllAssociative();

        if(count($user) === 0){
            echo 'User not found!';
        } else{
            $hashedPassword = $user[0]['password'];
            if(password_verify($_POST['password'], $hashedPassword) ){

                $status = new Redirect('/users/home/' . $user[0]['id']);

                session_start();
                $_SESSION['userid'] = $user[0]["id"];

            } else{
                echo 'Email or password is not correct!';
            }
        }
        return $status;
    }

}