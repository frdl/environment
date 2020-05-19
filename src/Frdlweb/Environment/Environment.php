<?php
namespace Frdlweb\Environment;


use Symfony\Component\Dotenv\Dotenv;
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
    protected $path = '';

    /**
     * File 
     *
     * @var  mixed
     */
    protected $root = '~/';

	
    protected $pfx = '.env.';
	
 public function __construct(?string $dir = null, ?string $pfx = null){	 
     if(!is_string($pfx)){
           $this->pfx = '.env.';
     }else{
          $this->pfx = $pfx;
     }	
	
     if(!is_string($dir)){
       //  $this->dir($this->getRootDir(null));
          $this->dir(getcwd());
     }else{
         $this->dir($dir);
     }
 }
    
 public function getRootDir($path = null){
	if(null===$path){
		$path = $_SERVER['DOCUMENT_ROOT'];
	}
		
  if(''!==dirname($path) && '/'!==dirname($path) //&& @chmod(dirname($path), 0755) 
    &&  true===@is_writable(dirname($path))
    ){
 	return $this->getRootDir(dirname($path));
  }else{
 	return $path;
  }
}   
    
    
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
    public function dir($root)
    {
      
        $root = realpath($root);
        
        if(!is_dir($root)){
          throw new \Exception(sprintf('%s is not a valid directory!', $root));  
        }
        
        $this->root = $root;
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
	$dotenv = new Dotenv();
        $environments = $this->getFromFile();
        foreach ($environments as $key => $value) {
            if (is_object($value)) {
                foreach ($value as $sub => $subValue) {
                  //  putenv("{$key}_{$sub}=$subValue");
		     $dotenv->populate([$key.'_'.$sub => $subValue], true);
                }
            } else {
              //  putenv("$key=$value");
		  $dotenv->populate([$key => $value], true);
            }
        }
    }

    /**
     * Getting from file 
     *
     * @return array
     */
    private function getFromFile()
    {
	    /*
        if ($this->file === false)
        {
            $filePath = getcwd().
                        $this->path.
                        '.env.'.
                        $this->environment;
        } 
        else 
        {
            $filePath = $this->file.
                        '.env.'.
                        $this->environment;
        }
	*/

	    $filePath = rtrim($this->root, '\\/')
		           .$this->path
		           .$this->pfx
                           .$this->environment;
	    
       if(file_exists($filePath)) {
	     $dotenv = new Dotenv();
            return $dotenv->parse(file_get_contents($filePath));
        }elseif(file_exists($filePath.'.json')) {
            return json_decode(file_get_contents($filePath.'.json'));
        }elseif(file_exists($filePath.'.json')) {
            return include($filePath.'.php');
        } 
        throw new Exception("Environment file is not found: $filePath.{json/php}");
    }

}
