<?php
/*
 * Copyright 2015 Kael.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Data validation class and business rules involving the User Authentication
 *
 * @author Kael
 */
require_once (__DIR__ . '/../daos/UserDAO.php');
define("MD5_LENGTH", 32);

class AuthenticationService {

    private $userDAO;
           
    public function __construct() 
    {
        $this->userDAO = new UserDAO();
    }
    
    public function validateEmail($email) 
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $result = $this->userDAO->searchUserByEmail($email);
            if ( ($result !== false) && (count($result) === 0) )
            {
               return true; 
            }           
        }        
        return false;
    }

    private function validatePassword($password) 
    {
        return ctype_xdigit($password) && (strlen($password) == MD5_LENGTH);
    }

    public function signIn($user) 
    {
        if ($user !== null &&
            $user->email !== null &&
            filter_var($user->email, FILTER_VALIDATE_EMAIL) &&    
            $user->password !== null &&
            $this->validatePassword($user->password)) 
        {              
            $results = $this->userDAO->searchUser($user);    
            if ( $results && (count($results) > 0) )
            {
                return $results[0]; //<! Returns first element in array
            }
            return array("error" => "Search by user failed.");
        }
        return array("error" => "Invalid values.");
    }
        
    public function signUp($user) 
    {
        if (($user !== null) &&
            ($user->email !== null) &&
            ($user->password !== null) &&
            $this->validateEmail($user->email) &&
            $this->validatePassword($user->password)
        ){              
            if($this->userDAO->create($user))
            {
                return $this->userDAO->searchUser($user)[0];
            }
            return array("error" => "Insert new user failed.");
        }
        return array("error" => "Invalid values.");
    }
}
