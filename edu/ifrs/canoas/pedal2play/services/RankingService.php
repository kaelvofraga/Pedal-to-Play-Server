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
 * Data validation class and business rules involving the Ranking
 *
 * @author Kael
 */
require_once (__DIR__ . '/../daos/RankingDAO.php');
define("R", 6371000); // metres

class RankingService {
        
    private $rankingDAO;
    
    public function __construct() 
    {
        $this->rankingDAO = new RankingDAO();
    }
    
    private function calcDistanceBetweenCoords($lat1, $lon1, $lat2, $lon2) 
    {
        /* From "Calculate distance, bearing and more between 
         * Latitude/Longitude points" (VENESS, 2015)   
         * http://www.movable-type.co.uk/scripts/latlong.html */    
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             sin($dLon / 2) * sin($dLon / 2) * cos(deg2rad($lat1)) * cos(deg2rad($lat2));
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = R * $c;
        
        return $d;
    }
    
    private function calculateDistance($path) 
    {
      $totalMeters = 0;
      $pathLen = count($path);
      for ($i = 0; $i < $pathLen - 1; $i++) {
        $totalMeters += $this->calcDistanceBetweenCoords(
            $path[$i]->coords->latitude, $path[$i]->coords->longitude,
            $path[$i + 1]->coords->latitude, $path[$i + 1]->coords->longitude);
      }

      return $totalMeters;
    }
    
    public function convertActivityInScore($activity, $userID) 
    {
        $score = $this->calculateDistance($activity->path);
        $this->rankingDAO->save($score, $userID);
    }
    
    public function getUserTotalScore($userID) 
    {
       $totalScore = 0;
       $scores = $this->rankingDAO->findAll($userID);
       if ($scores && count($scores) > 0) 
       {
           foreach ($scores as $score)
           {
              $totalScore += $score["score"]; 
           }
           return $totalScore;
       }
       return null;
    }
}
