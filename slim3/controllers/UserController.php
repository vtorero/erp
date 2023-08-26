<?php

use Illuminate\Database\Capsule\Manager as DB;

class UserController
{
    public function getUsers($request, $response, $args)
    {
        $users = DB::table('usuarios')->get();

        return $response->withJson($users);
    }
}