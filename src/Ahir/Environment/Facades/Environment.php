<?php

use Ahir\Facades\Facade;

class Environment extends Facade {

    /**
     * Get the connector name of main class
     *
     * @return string
     */
    public static function getFacadeAccessor() 
    { 
        return 'Ahir\Environment\Environment';
    }

}