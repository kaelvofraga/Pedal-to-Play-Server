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
 * Class for access the database involving User table as focus
 *
 * @author Kael
 */
require_once 'Connection.php';
define('TOKEN_BYTES', 16);

class UserDAO {

    private $conn;

    public function __construct() 
    {
        $this->conn = new Connection();
    }

    public function searchUser($user) 
    {
        if ($this->conn) 
        {
            $email = $this->conn->quote($user->email);
            $password = $this->conn->quote($user->password);
            
            return $this->conn->select(
                    "SELECT u.id_user, u.token FROM user u WHERE ". 
                    "u.email = $email AND u.password = $password;");
        }
        return array("error" => "Null connection.");
    }
    
    public function searchUserByEmail($email) 
    {
        if ($this->conn) 
        {
            $quotedEmail = $this->conn->quote($email);
            
            return $this->conn->select("SELECT u.id_user FROM user u WHERE ". 
                                       "u.email = $quotedEmail;");
        }
        return array("error" => "Null connection.");
    }
    
    public function validateToken($id, $token) 
    {
        if ($this->conn) 
        {
            $stmt = $this->conn->prepare("SELECT u.id_user FROM user u " . 
                                         "WHERE u.id_user = ? AND u.token = ?");
            $stmt->bind_param("is", $id, $token);
            if ($stmt->execute()) 
            {
                $res = $stmt->get_result();
                return $res ? ($res->num_rows > 0) : false;
            } 
            return false;
        }
        return false;
    }

    public function create($user) 
    {
        if ($this->conn) 
        {
            $email = $this->conn->quote($user->email);
            $password = $this->conn->quote($user->password);
            $token = $this->conn->quote(bin2hex(openssl_random_pseudo_bytes(TOKEN_BYTES)));
            $currentDate = $this->conn->quote((new DateTime())->format('Y-m-d H:i:s'));
            return $this->conn->query(
                "INSERT INTO user (email, password, token, subscription_date) ".
                "VALUES ($email, $password, $token, $currentDate);");
        }
        return array("error" => "Null connection.");
    }
        
    //TODO public function update($user) {}

    //TODO public function delete($user) {}

}
