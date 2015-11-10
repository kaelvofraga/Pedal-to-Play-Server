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
 * Data validation class and business rules involving the Activity Tracking
 *
 * @author Kael
 */
require_once ('RankingService.php');
require_once (__DIR__ . '/../daos/ActivityLogDAO.php');

class ActivityLogService {
    
    private $activityDAO;
    private $rankingService;
    
    public function __construct() 
    {
        $this->activityDAO = new ActivityLogDAO();
        $this->rankingService = new RankingService();
    }
    
    public function isValidPositions($path) 
    {
        foreach ($path as $position) 
        {
            if (!is_numeric($position->timestamp) ||
                !is_numeric($position->coords->latitude) ||
                !is_numeric($position->coords->longitude) ||
                (($position->coords->speed !== null) && 
                 !is_numeric($position->coords->speed))
            ) {
                return false;
            }
        }
        return true;
    }

    public function isValidActivity($activity) 
    {       
      if ($activity && 
          isset($activity->description) && 
          isset($activity->timer) && 
          isset($activity->path)
      ) {
        if (is_numeric($activity->timer) && 
            is_array($activity->path)
        ) {
           return $this->isValidPositions($activity->path);          
        }  
      }
      return false;
    }
    
    public function saveActivity($activity, $userID) 
    {
        if ($this->isValidActivity($activity)) 
        {
            if ($this->activityDAO->save($activity, $userID)) 
            {
                $this->rankingService->convertActivityInScore($activity, $userID);
                return true;
            }
        }
        return false;
    }
    
    public function findAll($userID) 
    {  
        $filteredActivities = array();
        $activities = $this->activityDAO->findAll($userID);
        if (!$activities) {
            return null;
        }
        foreach ($activities as $activity) {
            $filteredActivity = new stdClass();
            $filteredActivity->timestamp = strtotime($activity["date_time"]);
            $filteredActivity->calories = $activity["calories"];
            $filteredActivity->timer = $activity["timer"];
            $filteredActivity->description = $activity["description"];
            $filteredActivity->id = $activity["id_activitylog"];
            array_push($filteredActivities, $filteredActivity);
        }
        return $filteredActivities;
    }
    
    public function find($activityID) 
    {
        if (is_numeric($activityID)) 
        {
            $filteredPath = array();
            $path = $this->activityDAO->find($activityID);
            if(!$path) 
            {
              return null;  
            }
            foreach ($path as $position) 
            {   
                $filteredPosition = new stdClass();
                $filteredPosition->timestamp = strtotime($position["date_time"]);
                $filteredPosition->latitude = $position["latitude"];
                $filteredPosition->longitude = $position["longitude"];
                $filteredPosition->speed = $position["speed"];
                array_push($filteredPath, $filteredPosition);
            }
            return $filteredPath;
        }        
        return null;
    }
}
