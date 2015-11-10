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
 * Class for access the database involving Image table as focus
 *
 * @author Kael
 */
class ImageDAO {
    
    private $conn;

    public function __construct() 
    {
        $this->conn = new Connection();
    }
    
    public function getImageRequiredLevel($reference, $imageTypeID)
    {
        if ($this->conn) 
        {
            $quotedReference = $this->conn->quote($reference);
            $quotedTypeID = $this->conn->quote($imageTypeID);
            
            $result = $this->conn->select(
                    "SELECT I.required_level FROM image I ".
                    "WHERE I.id_typeimage = $quotedTypeID ".
                    "AND I.reference = $quotedReference;");
            
            return $result && (count($result) > 0) ? $result[0]["required_level"] : false;
        }
        return false;
    }
}
