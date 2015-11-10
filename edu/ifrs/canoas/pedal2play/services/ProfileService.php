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
 * Data validation class and business rules
 * involving the User personal informations
 *
 * @author Kael
 */
require_once ('RankingService.php');
require_once (__DIR__ . '/../daos/UserDAO.php');
define("DEFAULT_LEVEL", 1);
define("LEVEL_CONF_FILE", __DIR__ . '/../resources/level_conf.json');

class ProfileService {
    
    private $userDAO;
    private $rankingService;
           
    public function __construct() 
    {
        $this->userDAO = new UserDAO();
        $this->rankingService = new RankingService();
    }
    
    public function getUserLevel($userID) 
    {  
        $levelConfiguration = json_decode(file_get_contents(LEVEL_CONF_FILE));
        $totalScore = $this->rankingService->getUserTotalScore($userID);
        if ($totalScore && is_array($levelConfiguration))
        {
            foreach ($levelConfiguration as $levelRequirements)
            {
                if (is_object($levelRequirements) && 
                    ($totalScore >= $levelRequirements->requirements->score))
                {
                    return $levelRequirements->level;
                }
            }
        }
        return DEFAULT_LEVEL;
    }
    
    public function getMaxLevel() 
    {
        $levelConfiguration = json_decode(file_get_contents(LEVEL_CONF_FILE));
        if (is_array($levelConfiguration)) {
           return $levelConfiguration[0]->level;
        }
        return null;
    }
    
    public function getTotalScore($userID) 
    {       
        return $this->rankingService->getUserTotalScore($userID);
    }
}
