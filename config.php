<?php

class Config {
    var $config;

    function Config($filename) {
        $config = file_get_contents($filename);
        $this->config = json_decode($config);
    }

    function getConfig() {
        return $this->config;
    }


}