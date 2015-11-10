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
 * Pedal-to-Play WebAPI main file containing routes declarations
 *
 * @author Kael
 */
define('_BASE_PATH_', '/edu/ifrs/canoas/pedal2play/');
require_once (__DIR__ . _BASE_PATH_ . 'resources/Slim/Slim/Slim.php');
require_once (__DIR__ . _BASE_PATH_ . 'services/AuthenticationService.php');
require_once (__DIR__ . _BASE_PATH_ . 'services/TokenMiddleware.php');
require_once (__DIR__ . _BASE_PATH_ . 'services/AvatarService.php');
require_once (__DIR__ . _BASE_PATH_ . 'services/ProfileService.php');
require_once (__DIR__ . _BASE_PATH_ . 'services/ActivityLogService.php');

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->setName("Pedal-to-Play");
$app->add(new \TokenMiddleware());
$app->response()->header('Content-Type', 'application/json;charset=utf-8');
$app->log->setEnabled(true);
$app->log->setLevel(\Slim\Log::DEBUG);
$app->environment['slim.errors'] = fopen(__DIR__ .'/log.txt', 'w');

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Configurations ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

if (isset($_SERVER['HTTP_ORIGIN'])) 
{
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); //<! Cache for 1 day
}

/* Access-Control headers are received during OPTIONS requests */
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') 
{
    // return only the headers and not the content
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) 
    {
        header('Access-Control-Allow-Headers: content-type, Authorization');
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    exit;    
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~ End Configurations ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

function accessIsOK() 
{
    $app = \Slim\Slim::getInstance();
    if ($app->authenticated)
    {       
       return true;
    }
    $app->response()->status(401); //<! Unhautorized Error 
    return false;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Routes ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

$app->get('/', function () 
{
    echo "Welcome to P2P-WebAPI";   
});

$app->get('/validateemail/:email', function ($email) 
{
    $authService = new AuthenticationService();
    echo json_encode($authService->validateEmail($email));
});

$app->post('/signin', function() 
{
    $request = \Slim\Slim::getInstance()->request();
    $user = json_decode($request->getBody());
    $authService = new AuthenticationService();
    echo json_encode($authService->signIn($user));
});

$app->post('/signup', function()
{
    $request = \Slim\Slim::getInstance()->request();
    $user = json_decode($request->getBody());
    $authService = new AuthenticationService();
    echo json_encode($authService->signUp($user));    
});

$app->get('/avatar', function () 
{
    if (accessIsOK()) 
    {
        $request = \Slim\Slim::getInstance()->request();
        $userAuth = json_decode($request->headers->get('Authorization'));        
        $avatarService = new AvatarService();
        echo json_encode($avatarService->recover($userAuth->id)); 
    } 
});

$app->post('/avatar', function () 
{
    if (accessIsOK()) 
    {
        $request = \Slim\Slim::getInstance()->request();
        $userAuth = json_decode($request->headers->get('Authorization'));        
        $avatar = json_decode($request->getBody());
        $avatarService = new AvatarService();
        echo json_encode($avatarService->save($avatar, $userAuth->id)); 
    } 
});

$app->group('/user', function () use ($app) 
{
    $app->get('/level', function () 
    {
        if (accessIsOK()) 
        {
            $request = \Slim\Slim::getInstance()->request();
            $userAuth = json_decode($request->headers->get('Authorization'));
            $profileService = new ProfileService();
            echo json_encode($profileService->getUserLevel($userAuth->id));
        }
    });
    
    $app->get('/score', function () 
    {
        if (accessIsOK()) 
        {
            $request = \Slim\Slim::getInstance()->request();
            $userAuth = json_decode($request->headers->get('Authorization'));
            $profileService = new ProfileService();
            echo json_encode($profileService->getTotalScore($userAuth->id));
        }
    });
    
    $app->get('/maxlevel', function () 
    {
        if (accessIsOK()) 
        {
            $profileService = new ProfileService();
            echo json_encode($profileService->getMaxLevel());
        }
    });    
});

$app->get('/activities', function () 
{
    if (accessIsOK()) 
    {
        $request = \Slim\Slim::getInstance()->request();
        $userAuth = json_decode($request->headers->get('Authorization'));        
        $activityService = new ActivityLogService();
        echo json_encode($activityService->findAll($userAuth->id)); 
    } 
});

$app->get('/activity/:id', function ($id) 
{
    if (accessIsOK()) 
    {
        $activityService = new ActivityLogService();
        echo json_encode($activityService->find($id)); 
    } 
});

$app->post('/activity', function () 
{
    if (accessIsOK()) 
    {
        $request = \Slim\Slim::getInstance()->request();
        $userAuth = json_decode($request->headers->get('Authorization'));        
        $activity = json_decode($request->getBody());
        $activityService = new ActivityLogService();
        echo json_encode($activityService->saveActivity($activity, $userAuth->id)); 
    } 
});

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ End Routes ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

/* 404 Handle */
$app->notFound(
    function () use ($app) {
        $app->halt(
            404,
            json_encode(array('status' => 404, 'message' => 'Not found'))
        );
    }
);

$app->run();
