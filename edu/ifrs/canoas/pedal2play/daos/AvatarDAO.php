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
 * Class for access the database involving Avatar table as focus
 *
 * @author Kael
 */
require_once 'Connection.php';

class AvatarDAO {
    
    private $conn;

    public function __construct() 
    {
        $this->conn = new Connection();
    }
    
    public function getAvatarByUser($userID)
    {
        if ($this->conn) 
        {
            $quotedUserID = $this->conn->quote($userID);
            $result = $this->conn->select(
                    "SELECT A.id_avatar, A.skin_color, A.gender " .
                    "FROM avatar A WHERE A.id_user = $quotedUserID;");
            return $result && (count($result) > 0) ? $result[0] : false;
        }
        return false;
    }

    public function getUserAvatarID($userID)
    {
        if ($this->conn) 
        {
            $quotedUserID = $this->conn->quote($userID);
            $result = $this->conn->select(
                "SELECT A.id_avatar FROM avatar A JOIN user U ".  
                "WHERE A.id_user = U.id_user AND U.id_user = $quotedUserID;");
            
            return $result && (count($result) > 0) ? $result[0]["id_avatar"] : false;
        }
        return false;
    }
    
    public function create($avatar, $userID) 
    {
        if ($this->conn) 
        {
            $quotedUserID = $this->conn->quote($userID);
            $quotedGender = $this->conn->quote($avatar->gender);
            $quotedSkinColor = $this->conn->quote($avatar->skinColor);
            
            return $this->conn->query(
                "INSERT INTO avatar (id_user, gender, skin_color) ".
                "VALUES ($quotedUserID, $quotedGender, $quotedSkinColor);");
        }
        return false;
    }
    
    public function update($avatar, $userID) 
    {
        if ($this->conn) 
        {
            $quotedUserID = $this->conn->quote($userID);
            $quotedGender = $this->conn->quote($avatar->gender);
            $quotedSkinColor = $this->conn->quote($avatar->skinColor);
 
            return $this->conn->query(
                "UPDATE avatar SET gender = $quotedGender, ".
                "skin_color = $quotedSkinColor WHERE id_user = $quotedUserID;");
        }
        return false;
    }
}
