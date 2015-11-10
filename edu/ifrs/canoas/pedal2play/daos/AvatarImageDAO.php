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
 * Class for access the database involving AvatarImages table as focus
 *
 * @author Kael
 */
require_once 'Connection.php';

class AvatarImageDAO {
    
    private $conn;

    public function __construct() 
    {
        $this->conn = new Connection();
    }
    
    private function rollBackAvatar($avatarID) 
    {
        if ($this->conn) 
        { 
            if ($this->conn->query(
                    "DELETE FROM `avatar_image` WHERE id_avatar = $avatarID;")
            ) {
                $this->conn->query(
                    "DELETE FROM `avatar` WHERE id_avatar = $avatarID;");
            }           
        }
    }

    public function createAvatarImages($avatar) 
    {
        if ($this->conn) 
        {   
            $avatarID = $this->conn->quote($avatar->id);
            for ($i = 0; $i < count($avatar->pieces); $i++) 
            {   
                $typeID = $this->conn->quote($i + 1);
                $imageReference = $this->conn->quote($avatar->pieces[$i] + 1);	
                
                if ($this->conn->query(
                    "INSERT INTO avatar_image(id_avatar, id_image, color) ".
                    "VALUES ($avatarID,(SELECT I.id_image FROM image I ". 
                    "WHERE I.id_typeimage = $typeID ". 
                    "AND I.reference = $imageReference), '#ffffff');") === false
                ) { 
                    $this->rollBackAvatar($avatarID);
                    return array("error" => "Avatar saving failed");
                }
            }
            return true; 
        }
        return array("error" => "Null connection.");
    }    
    
    private function getCurrentImageID($quotedAvatarID, $quotedTypeImgID) {
        if ($this->conn)
        {     
            $result = $this->conn->select(
                "SELECT I.id_image FROM image I JOIN avatar_image AI ".
                "WHERE AI.id_image = I.id_image AND ". 
                "AI.id_avatar = $quotedAvatarID AND ".
                "I.id_typeimage = $quotedTypeImgID;");
            return $result && (count($result) > 0) ? $result[0]["id_image"] : false;
        }
        return false;      
    }
    
    public function updateAvatarImages($avatar) 
    {
        if ($this->conn) 
        {   
            $avatarID = $this->conn->quote($avatar->id);
            for ($i = 0; $i < count($avatar->pieces); $i++) 
            {                
                $imageReference = $this->conn->quote($avatar->pieces[$i] + 1);	
                $typeID = $this->conn->quote($i + 1);                           
                $imgID = $this->conn->quote($this->getCurrentImageID($avatarID, $typeID));                
                if ($this->conn->query(
                    "UPDATE avatar_image AI SET AI.id_image = ". 
                    "(SELECT I.id_image FROM image I WHERE I.id_typeimage = $typeID ".
                    "AND I.reference = $imageReference), AI.color = '#ffffff' ".				   
                    "WHERE AI.id_avatar = $avatarID AND AI.id_image = $imgID;") === false
                ) {
                    return array("error" => "Avatar saving failed");
                }
            }
            return true; 
        }
        return array("error" => "Null connection.");
    }
    
    public function getImagesReferences($avatarID) 
    {
        if ($this->conn) 
        {   
            $quotedAvatarID = $this->conn->quote($avatarID);
            return $this->conn->select(
                    "SELECT I.id_typeimage, I.reference " .
                    "FROM image I JOIN avatar_image AI ".
                    "WHERE I.id_image = AI.id_image " .
                    "AND AI.id_avatar = $quotedAvatarID " .
                    "ORDER BY I.id_typeimage;");
        }
        return null;
    }
}
