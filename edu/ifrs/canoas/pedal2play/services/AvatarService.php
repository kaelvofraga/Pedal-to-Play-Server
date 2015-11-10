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
 * Data validation class and business rules involving the Avatar
 *
 * @author Kael
 */
require_once (__DIR__ . '/../daos/AvatarImageDAO.php');
require_once (__DIR__ . '/../daos/AvatarDAO.php');
require_once (__DIR__ . '/../daos/ImageDAO.php');
require_once ('ProfileService.php');

class AvatarService {
    
    private $avatarDAO;
    private $imageDAO;
    private $avatarImageDAO;
    private $profileService;
        
    public function __construct() 
    {
        $this->avatarDAO = new AvatarDAO();
        $this->imageDAO = new ImageDAO();
        $this->avatarImageDAO = new AvatarImageDAO();
        $this->profileService = new ProfileService();
    }
    
    public function validatePieces($pieces, $userID) 
    {
        $userLevel = $this->profileService->getUserLevel($userID);
        for ($imgTypeID = 0; $imgTypeID < count($pieces); $imgTypeID++) 
        {        
            if ($userLevel <
                $this->imageDAO->getImageRequiredLevel($pieces[$imgTypeID]+1, $imgTypeID+1))
            {
                return false;
            }
        }
        return true;
    }
    
    private function updateAvatar($avatar, $userID)
    {
        if ($this->avatarDAO->update($avatar, $userID) !== false) 
        {
            return $this->avatarImageDAO->updateAvatarImages($avatar);   
        }
        return array("error" => "Avatar saving failed");
    }
    
    private function createAvatar($avatar, $userID)
    {
        if ($this->avatarDAO->create($avatar, $userID)) 
        {
            $avatar->id = $this->avatarDAO->getUserAvatarID($userID);
            if ($avatar->id !== false) {
                return $this->avatarImageDAO->createAvatarImages($avatar); 
            }
        }
        return array("error" => "Avatar saving failed");
    }

    public function save($avatar, $userID) 
    {        
        if (($avatar !== null) &&
            ($avatar->pieces !== null) &&
            ($avatar->gender !== null) &&
            ($avatar->skinColor !== null) &&
            $this->validatePieces($avatar->pieces, $userID)
        ){  
            $avatar->id = $this->avatarDAO->getUserAvatarID($userID);
            if ($avatar->id !== false) {
               return $this->updateAvatar($avatar, $userID);               
            } else {
               return $this->createAvatar($avatar, $userID); 
            }
        }
        return array("error" => "Invalid avatar values.");
    }
    
    private function generateAvatarPieces($avatar, $references) 
    {
        $avatarPieces = new stdClass;
        $avatarPieces->skinColor = $avatar['skin_color'];
        $avatarPieces->gender = $avatar['gender'];
        $avatarPieces->pieces = array();
        
        foreach ($references as $image) {
            $avatarPieces->pieces[$image['id_typeimage'] - 1] = $image['reference'] - 1;                    
        }
        
        return $avatarPieces;
    }
    
    public function recover($userID) {
        $avatar = $this->avatarDAO->getAvatarByUser($userID);
        if ($avatar)
        {
            $references = $this->avatarImageDAO->getImagesReferences($avatar['id_avatar']);
            if ($references && (count($references) > 0))
            {
                return $this->generateAvatarPieces($avatar, $references);
            }
            return array("error" => "Image references not found.");
        }
        return array("error" => "Avatar not found.");
    }
}
