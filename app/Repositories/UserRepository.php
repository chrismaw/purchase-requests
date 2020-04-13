<?php

namespace App\Repositories;

use Auth0\Login\Contract\Auth0UserRepository as Auth0UserRepository;
use App\User as User;

class UserRepository implements Auth0UserRepository
{
    public function getUserByDecodedJWT($jwt)
    {
        $jwt->user_id = $jwt->sub;
        return $this->upsertUser($jwt);
    }

    public function getUserByUserInfo($userInfo)
    {
        return $this->upsertUser((object)$userInfo['profile']);
    }

    /**
     * Check if user is in database, if not create.
     *
     * @return User
     */
    protected function upsertUser($profile)
    {
        $user = User::where("sub", $profile->sub)->first();
        // create user if not in database
        if ($user === null) {
            $user = new User();
            $user->email = $profile->email;
            $user->sub = $profile->sub;
            $user->name = $profile->name;
            // random password, we dont need it
            $user->password = md5(time());
            $user->created_at(date('Y-m-d H:i:s'));
            $user->save();
        }
        return $user;
    }

    public function getUserByIdentifier($identifier)
    {
        //Get the user info                           of the user logged in (probably in session)
        $user = \App::make('auth0')->getUser();
        if ($user === null) return null;
        // build the user
        $user = $this->getUserByUserInfo($user);
        // it is not the same user as logged in, it is not valid
        if ($user && $user->id == $identifier) {
            return $user;
        }
    }
}
