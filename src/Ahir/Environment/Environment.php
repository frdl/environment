<?php namespace Ahir\Environment;

use Exception;

class Environment {

    /**
     * Active environment variable 
     *
     * @var string
     */
    protected $environment = 'production';

    /**
     * Environment detection 
     *
     * @param  array        $setups 
     * @return null
     */
    public function detectEnvironment(Array $setups)
    {
        foreach ($setups as $environment => $setup) {
            foreach ($setup as $index => $hostname) {
                if ($hostname === gethostname()) {
                    $this->environment = $environment;
                }
            }
        }
        $this->loadEnvironmentVariables();
    }

    /**
     * Loading environment variables 
     *
     * @return null
     */
    private function loadEnvironmentVariables()
    {
        $environments = $this->getFromFile();
        foreach ($environments as $key => $value) {
            putenv("$key=$value");
        }
    }

    /**
     * Getting from file 
     *
     * @return array
     */
    private function getFromFile()
    {
        $filePath = getcwd().'/.env.'.$this->environment.'.php';
        if (!file_exists($filePath)) {
            throw new Exception("Environment file is not found: $filePath");
        }
        return include($filePath);
    }

}