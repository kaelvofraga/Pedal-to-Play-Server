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
 * Class for access the database involving ActivityLog table as focus
 *
 * @author Kael
 */
require_once 'Connection.php';

class ActivityLogDAO {
    
    private $conn;

    public function __construct() 
    {
        $this->conn = new Connection();
    }
    
    public function savePath($activityID, $path)
    {
        if ($this->conn) 
        {
            $timestamp = null;
            $latitude = null;
            $longitude = null;
            $speed = null;
            
            $stmt = $this->conn->prepare("INSERT INTO `position` " .
                "(`id_activitylog`, `date_time`, `latitude`, `longitude`, `speed`) " .
                "VALUES (?, FROM_UNIXTIME(?), ?, ?, ?)");
            $stmt->bind_param("iiddd", $activityID, $timestamp, $latitude, $longitude , $speed);
                        
            foreach ($path as $position) 
            {
                $timestamp = floor($position->timestamp/1000);
                $latitude = $position->coords->latitude;
                $longitude = $position->coords->longitude;
                $speed = $position->coords->speed || 0.0;
                
                $stmt->execute();
            }
        }
    }
    
    public function save ($activity, $userID) {
        if ($this->conn) 
        {              
            $calories = 0;            
            $firstPositionTime = ($activity->path[0]->timestamp)/1000;                    
            $stmt = $this->conn->prepare("INSERT INTO `activitylog` " .
                "(`calories`, `id_user`, `description`, `timer`, `date_time`) ".
                "VALUES (?, ?, ?, ?, FROM_UNIXTIME(?))");              
            $stmt->bind_param("disii", $calories, $userID, $activity->description, 
                $activity->timer, $firstPositionTime);
            if ($stmt->execute()) {
               $id = $this->conn->getLastID();
               $this->savePath($id, $activity->path);
               return true;
            } else {
               $errorMessage = array();
               $errorMessage->error = $stmt->error;
               return $errorMessage; 
            }
        }
        return false;
    }
    
    public function findAll($userID) {
        if ($this->conn) 
        {
            $quotedUserID = $this->conn->quote($userID);
            $result = $this->conn->select(
                "SELECT * FROM `activitylog` WHERE id_user = $quotedUserID"
            );
            return $result && (count($result) > 0) ? $result : null;
        }
        return null;
    }
    
    public function find($activityID) {
        if ($this->conn) 
        {
            $quotedActivityID = $this->conn->quote($activityID);
            $result = $this->conn->select(
                "SELECT * FROM `position` WHERE id_activitylog = $quotedActivityID"
            );
            return $result && (count($result) > 0) ? $result : null;
        }
        return null;
    }
}
