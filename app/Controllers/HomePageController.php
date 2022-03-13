<?php

namespace App\Controllers;

use App\View;

class HomePageController
{
    public function homePage(): View
    {
        return new View('homePage');
    }

    public function userHomePage(): View
    {
        $userName = $_SESSION['username'];
        $userId = (int) $_SESSION['userid'];
        return new View('home', [
            'username' => $userName,
            'userid' => $userId
        ]);
    }
}