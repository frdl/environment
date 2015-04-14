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
     * Path prefix for environment files 
     *
     * @var string
     */
    protected $path = '/';

    /**
     * File 
     *
     * @var  mixed
     */
    private $file = false;

    /**
     * Setting path prefix 
     *
     * @param  string       $path
     * @return this
     */
    public function path($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Setting file 
     *
     * @param  string       $file 
     * @return this
     */
    public function file($file)
    {
        $this->file = $file;
        return $this;
    }

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
        if ($this->file === false)
        {
            $filePath = getcwd().
                        $this->path.
                        '.env.'.
                        $this->environment.
                        '.php';            
        } 
        else 
        {
            $filePath = $this->file.
                        '.env.'.
                        $this->environment.
                        '.php';            
        }

        if (!file_exists($filePath)) {
            throw new Exception("Environment file is not found: $filePath");
        }
        return include($filePath);
    }

}