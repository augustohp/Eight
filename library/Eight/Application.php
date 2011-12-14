<?php
namespace Eight;

use \UnexpectedValueException   as Value;
use \RecursiveIteratorIterator  as Iterator;
use \RecursiveDirectoryIterator as Dir;
use Respect\Config\Container    as Config;
use Respect\Rest\Router         as Http;
use Eight\Database\Mapper       as Mapper;
use Respect\Rest\Request;
use Eight\Entity\Owner;

define('DO_NOT_THROW_EXCEPTION', false);

/**
 * Glue it all togheter, somehow.
 *
 * @package Eight
 * @since   0.1.0
 * @author  Augusto Pascutti <augusto@phpsp.org.br>
 */
class Application
{
    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var string
     */
    protected $path;
    /**
     * @var Respect\Config\Container
     */
    protected $config;
    /**
     * @var Respect\Rest\Router
     */
    protected $http;
    /**
     * @var Twig_Environment
     */
    protected $twig;
    /**
     * @var Eight\Database\Mapper
     */
    protected $mapper;
	/**
	 * @var Eight\Entity\Owner
	 */
	protected $owner;
    /**
     * Class cache.
     *
     * @var array
     */
    private $cache = array();
    
    /**
     * Constructor.
     *
     * @throws  UnexpectedValueException    Invalid app path
     * @throws  UnexpectedValueException    Config not found
     * @param   string  $appPath The path to the app directory
     */
    public function __construct($appPath)
    {
        $this->path      = realpath($appPath);
        if (empty($this->path))
            throw new Value("'{$appPath}' is not a valid directory");
        
        $config = $this->getPath('config.ini');
        if (!file_exists($config))
            throw new Value("Configuration file 'config.ini' not found in {$config}");
    }
    
    /**
     * Returns a filesystem path related to the app directory.
     *
     * @param   string[optional] $suffix 
     * @return  string
     */
    public function getPath($suffix=null)
    {
        return $this->path.DIRECTORY_SEPARATOR.$suffix;
    }
    
    /**
     * Returns a namespaced name (class, trait, interface) with the 
     * app namespace.
     *
     * @param   string  $name 
     * @return  string
     */
    public function getNamespace($name)
    {
        return $this->namespace.'\\'.$name;
    }
    
    /**
     * Parses the configuration file and adds the app dir to the include_path.
     *
     * @throws  UnexpectedValueException    No database mapper defined in config
     * @param   string[optional]  $configFile 
     * @return  Eight\Application
     */
    public function parseConfiguration($configFile=null)
    {
        set_include_path($this->getPath('../').PATH_SEPARATOR.get_include_path());
        $configFile   = $configFile ?: $this->getPath('config.ini') ;
        $this->config = new Config($configFile);
        // Uses the application configuration for things
        foreach ($this->config->app as $key=>$value)
            switch ($key) {
                case 'namespace':
                    $this->namespace = $value;
                    break;
            }
        return $this;
    }
    
    /**
     * Register all application HTTP routes.
     *
     * @return Eight\Application
     */
    protected function registerHttpRoutes()
    {
        // Creating HTTP router
        $this->http   = new Http($this->config->app['url']);
        $this->http->isAutoDispatched = false;
        $routesDir    = $this->getPath('Route');
        $iterator     = new Iterator(new Dir($routesDir));
        $allowedRoutes= $this->config->app['public_routes'];
        $self         = $this;
        foreach ($iterator as $file) {
            $path      = $file->getRealpath();
            $name      = explode($routesDir.DIRECTORY_SEPARATOR,$path);
            $class     = str_replace(array('.php', '/'), array('', '\\'), end($name));
            $class     = $this->getNamespace('Route\\'.$class);
            $url       = $class::URL;
            $construct = array($this);

            // Route a given class to its URI
            $this->http->any($url, $class, $construct)
                 ->by($this->auth($url))
                 ->acceptLanguage($this->langs())
                 ->accept(array(
                    'text/html' => function($data) use ($self) {
                        header('Content-Type: text/html; charset=UTF-8');
                        if (!isset($data['view']))
                            return $data;
                        echo $self->twig()->render($data['view'], $data);
                    },
                    'application/json'=> function($data) use ($self) {
                        header('Content-Type: application/json; charset=UTF-8');
                        unset($data['view']);
                        echo json_encode($data);
                    }
                 ));
        }
        return $this;
    }

    /**
     * Returns an array of callbacks for the language accepted by the 
     * application.
     *
     * @return array
     */
    protected function langs()
    {
        if (isset($this->cache['langs']))
            return $this->cache['langs'];

        $langPath     = $this->getPath('Language');
        if (!file_exists($langPath))
            return array(); // No languages found

        $langsDir     = new Iterator(new Dir($langPath));
        $langs        = array();
        foreach ($langsDir as $langFile) {
            $lang         = str_replace('.ini', '', $langFile->getFilename());
            $filename     = $langFile->getRealPath();
            $strings      = parse_ini_file($filename);
            $langs[$lang] = function ($data=array()) use ($strings) { $data = $data ?: array(); return array_merge($data, $strings); };
        }
        $this->cache['langs'] = $langs;
        return $langs;
    }
    
    /**
     * Handles the authentication of the application.
     * 
     * @return	function
     */
    protected function auth($route)
    {
        $config = $this->config->app;
		$owner  = $this->getOwner(DO_NOT_THROW_EXCEPTION);
        return function() use ($config, $route, $owner) {
			// Authenticated ?
	        if ($owner instanceof Owner)
				return true; // Allow access to everything

            // The URL is a public route?
            if (isset($config['public_routes']) && in_array($route, $config['public_routes']))
                return true; // Allow access for public route

			// No access, redirect
			header('Location: /auth');
			return false;
        };
    }
    
    /**
     * Dispatches the current request.
     */
    public function run()
    {
        $this->parseConfiguration()->registerHttpRoutes();
        if (isset($_SERVER['REQUEST_URI']))
            $this->http->run();
    }

	/**
	 * Returns the owner loggeed in the application.
	 *
	 * @throws	UnexpectedValueException
	 * @param	boolean[optional]	$throwException
	 * @return 	Eight\Entity\Owner|false
	 */
	public function getOwner($throwException = true)
	{
		if ($this->owner instanceof Eight\Entity\Owner)
			return $this->owner;

		if (isset($_SESSION['owner'])) {
			$this->owner = unserialize($_SESSION['owner']);
			return $this->owner;
		}

		if ($throwException)
			throw new Value('No owner defined, this is serius!');
		return false;
	}
	
    
    /**
     * Returns the twig instance.
     *
     * @return Twig_Environment
     */
    public function twig()
    {
        if ($this->twig)
            return $this->twig;
        
        if (!$this->config)
            throw new Value('Twig is not configured to be used for Views!');

        require 'Twig/Autoloader.php';
        \Twig_Autoloader::register();
        $viewsDir = $this->getPath('View');
        $loader   = new \Twig_Loader_Filesystem($viewsDir);
        $twig     = new \Twig_Environment($loader, $this->config->twig);
        return $this->twig = $twig;
    }
    
    /**
     * Gets the database mapper.
     *
     * @return Eight\Database\Mapper
     */
    public function db()
    {
        if ($this->mapper)
            return $this->mapper;

        // Gets the mapper
        if (!isset($this->config->mapper) || !$this->config->mapper instanceof Mapper)
            throw new Value('Database Mapper not (correctly) defined');

        $this->mapper = $this->config->mapper;        
        return $this->mapper;
    }
}