<?php

namespace App\Controllers;

use App\View;

class LogOutController
{
    function logOut(): View
    {
        session_unset();
        session_destroy();
        return new View('Users/logout');
    }
}