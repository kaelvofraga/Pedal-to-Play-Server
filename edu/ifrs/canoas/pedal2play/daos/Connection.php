<?php

/*
  The MIT License (MIT)

  Copyright (c) 2014

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
 */

class Connection {

    // The database connection
    protected static $connection;

    /**
     * Connect to the database
     * 
     * @return bool false on failure / mysqli MySQLi object instance on success
     */
    public function connect() {
        if (!isset(self::$connection)) {
            $config = parse_ini_file(__DIR__ . '/../resources/dbconfig.ini');
            self::$connection = new mysqli($config['host'], 
                                           $config['username'], 
                                           $config['password'], 
                                           $config['dbname']);
        }

        if (self::$connection === false) {
            return false;
        }

        return self::$connection;
    }

    /**
     * Query the database
     *
     * @param $query The query string
     * @return mixed The result of the mysqli::query() function
     */
    public function query($query) {
        $connection = $this->connect();

        $result = $connection->query($query);

        return $result;
    }

    /**
     * Fetch rows from the database (SELECT query)
     *
     * @param $query The query string
     * @return bool False on failure / array Database rows on success
     */
    public function select($query) {
        $rows = array();
        $result = $this->query($query);
        if ($result === false) {
            return false;
        }
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Fetch the last error from the database
     * 
     * @return string Database error message
     */
    public function error() {
        $connection = $this->connect();
        return $connection->error;
    }

    /**
     * Quote and escape value for use in a database query
     *
     * @param string $value The value to be quoted and escaped
     * @return string The quoted and escaped string
     */
    public function quote($value) {
        $connection = $this->connect();
        return "'" . $connection->real_escape_string($value) . "'";
    }
    
    /**
     * Gets the number of affected rows in a previous MySQL 
     *
     * @return int The number of affected rows
     */
    public function countAffectedRows() {
        $connection= $this->connect();
        return $connection->affected_rows;
    }
    
    /**
     * Prepare an SQL statement for execution 
     *
     * @param string $value The query string
     * @return returns a statement object or FALSE if an error occurred.
     */
    public function prepare($value) {
        $connection = $this->connect();
        return $connection->prepare($value);
    }

    /**
     *  Returns auto generated id of the last transaction 
     *
     * @return mixed last auto generated id
     */
    public function getLastID() {
        $connection = $this->connect();
        return $connection->insert_id;
    }
}
