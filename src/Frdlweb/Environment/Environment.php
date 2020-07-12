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
    protected $file = '';

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
	
    protected $host = null;	
	
	protected $required = true;
	
 public function __construct(?string $dir = null, ?string $pfx = null){	 
	 
     $this->host = $this->getHost();     
	 
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
      
      //  $root = realpath($root);
 
	    
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

            foreach ($setup['hosts'] as $index => $hostname) {
                if ('*' ===$hostname || $hostname === $this->host) {		
					if(isset($setup['required']) && is_bool($setup['required'])){			
						$this->required = $setup['required'];			
					}else{
						$this->required = true;			
					}
                 
			$this->file = $environment;		     
			$this->loadEnvironmentVariables();
                }
            }
        }
      return $this;
    }

	
    public function host(?string $host = null){
	    if(null!==$host){
		$this->host = $host;    
	    }else{
		$this->host = $this->getHost();     
	    }
    }
	
	
   public function getHost(){
	if(isset($_SERVER['SERVER_NAME'])){
	  return $_SERVER['SERVER_NAME'];	
	}elseif(isset($_SERVER['HTTP_HOST'])){
	  return $_SERVER['HTTP_HOST'];	
	}elseif(isset($_SERVER['HTTP_X_FORWARDED_HOST'])){
	  return $_SERVER['HTTP_X_FORWARDED_HOST'];	
	}else{
	  return gethostname();	
	}
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
                    putenv("{$key}_{$sub}=$subValue");
		          $dotenv->populate([$key.'_'.$sub => $subValue], true);
                }
            } else {
                    putenv("$key=$value");
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
        $root  = $this->root;
	    
       if(!\is_array($root)){
	       $root = [$root];	   
       }
	    
	
	 foreach($root as $d){
	   $p ='';
	   $res =[];
	    $tokens = \explode('\\/', $d);
		 foreach($tokens as $t){ 
			$p .= \DIRECTORY_SEPARATOR.$t; 
			 if(getenv('HOME') === \substr($p, 0, strlen(getenv('HOME')))){
				continue; 
			 }
			 
	   
			    $filePath = rtrim(		        
				    rtrim($p, '\\/').\DIRECTORY_SEPARATOR     
		           
				    .rtrim($this->path, '\\/').\DIRECTORY_SEPARATOR
		         
				    .$this->pfx
                         
				    .$this->file,
		   
				    '.'
	 
			    );
	    
       if(file_exists($filePath)) {
	     $dotenv = new Dotenv();
            $res = $dotenv->parse(file_get_contents($filePath));
        }elseif(file_exists($filePath.'.json')) {
            $res = json_decode(file_get_contents($filePath.'.json'));
        }elseif(file_exists($filePath.'.php')) {
            $res = require($filePath.'.php');
        } 
		if(true === $this->required){
                     throw new Exception("Environment file is not found: $filePath{|.json|.php}");
		}else{
		  return $res;	
		}
		 
	 }
		
	 
	 }
	 
	 return $res;
    }

}
