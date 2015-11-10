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
 * Routines for validate the access to web service routes
 *
 * @author Kael
 */
require_once (__DIR__ . '/../resources/Slim/Slim/Middleware.php');
require_once (__DIR__ . '/../daos/UserDAO.php');

class TokenMiddleware extends \Slim\Middleware {

    public function __construct() {}

    /**
     * Call
     */
    public function call() 
    {
        $jsonObject = $this->app->request()->headers()->get('Authorization');        
        $userAuth = json_decode($jsonObject);
        $userDAO = new UserDAO();
        if (($userAuth !== null) && 
            (is_numeric($userAuth->id)) &&   
            ($userDAO->validateToken($userAuth->id, $userAuth->token))
        ){
            $this->app->authenticated = true;
        } 
        else
        {
            $this->app->authenticated = false;
        }
        $this->next->call();
    }
    
}