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
 * Class for access the database involving Ranking table as focus
 *
 * @author Kael
 */
require_once 'Connection.php';

class RankingDAO {
    
    private $conn;

    public function __construct() 
    {
        $this->conn = new Connection();
    }
    
    public function save($score, $userID) 
    {
        if ($this->conn) 
        {
            if (is_numeric($score))
            {
               $quotedScore = $this->conn->quote($score);
               $quotedUserID = $this->conn->quote($userID);
               $today = time();
                       
               return $this->conn->query(
                "INSERT INTO `ranking`(`score`, `update_date`, `id_user`) ".
                "VALUES ($quotedScore, FROM_UNIXTIME($today), $quotedUserID);"
               ); 
            }
        }
        return false;
    }
    
    public function findAll($userID) 
    {
        if ($this->conn) 
        {
            $quotedUserID = $this->conn->quote($userID);
            return $this->conn->select(
                "SELECT `score` FROM `ranking` WHERE `id_user` = $quotedUserID;"               
            );                    
        }
        return null;
    }
}
