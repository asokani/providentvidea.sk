<?php

class Db {
    var $connection;
    var $error = false;

    function Db($config) {
        $this->connection = mysqli_connect("localhost", $config->username, $config->password);

        if (!$this->connection) {
            $this->error = true;
            return;
        }

        mysqli_select_db($this->connection, $config->database);
        mysqli_query($this->connection, "set names utf8");

    }

    function query($query) {
        $result = mysqli_query($this->connection, $query);

        if (!$result) {
            $this->error = true;
            return;
        }

        return $result;
    }

    function error() {
        return $this->error;
    }

    function num_rows($result) {
        return mysqli_num_rows($result);
    }

    function fetch_row($result) {
        return mysqli_fetch_assoc($result);
    }
}

?>