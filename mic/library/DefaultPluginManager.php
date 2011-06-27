<?php

namespace Mic\Library;

use Mic\Interfaces\Request;
use Mic\Interfaces\Plugin;
use Mic\Interfaces\Uri;
use Mic\Interfaces\Cache;
use Mic\Interfaces\PluginManager;

use SplPriorityQueue;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DefaultPluginManager implements PluginManager
{
	protected $_pluginDirectories;
	
	protected $_fileExtension;
		
	/**
	 * Holds the cache for performance speedup
	 * @var Cache
	 */
	protected $_cache;
	
	/**
	 * Holds the reques URI
	 * @var Uri
	 */
	protected $_uri;
	
	protected $_requestMethod;
	
	private $_plugins;
	
	private $_cacheKey;
	
	private $_lastMatch;
		
	public function __construct($path, $fileExtension = 'php')
	{
		$this->_cacheKey = 'mic-plugins';
		$this->_pluginDirectories = explode(PATH_SEPARATOR, $path);
		$this->_fileExtension = $fileExtension;	
	}
	
	public function setCache(Cache $cache)
	{
		$this->_cache = $cache;
	}
	
	public function invoke($method, $argument)
	{
		if (!isset($this->_plugins)) {
			$this->_getPluginList();
			$this->_sortPlugins();
		}
		
		foreach ($this->_plugins as $plugin) {
			if ($this->_isRequestMatchedByPluginRoute($plugin)) {
				$plugin->$method($argument);
			}
		}
	}
	
	public function setRequest(Request $request)
	{
		$this->_uri = $request->getUri();
		$this->_requestMethod = $request->getMethod();
	}
	
	private function _sortPlugins()
	{
		$priorityQueue = new SplPriorityQueue();
		
		foreach ($this->_plugins as $plugin) {
			$priority = -1 * $plugin->getPreferredOrder();
			$priorityQueue->insert($plugin, $priority);
		}
		
		$this->_plugins = array();
		foreach ($priorityQueue as $plugin) {
			$this->_plugins[] = $plugin;
		}
	}
	
	private function _getPluginList()
	{
		if (isset($this->_cache) && $this->_cache->exists($this->_cacheKey)) {
			$this->_plugins = $this->_cache->get($this->_cacheKey);
			return;
		}
			
		$this->_generatePluginList();	
		if (isset($this->_cache) && !$this->_cache->exists($this->_cacheKey)) {
			$this->_cache->store($this->_cacheKey, $this->_plugins);
		}
	}
	
	private function _generatePluginList()
	{
		$pluginList = array();
		foreach ($this->_pluginDirectories as $directory) {
			$fileList = new RecursiveDirectoryIterator($directory);
			$list = new RecursiveIteratorIterator($fileList);
			foreach ($list as $file) {
				$extension = pathinfo($file->getBasename(), PATHINFO_EXTENSION);
				if ($extension == $this->_fileExtension) {
					$plugin = $this->_loadPluginFile($file->getPathname());
					if ($plugin instanceof Plugin) {
						$this->_plugins[] = $plugin;
					}
				}
			}
		}
	}
	
	private function _isRequestMatchedByPluginRoute(Plugin $plugin)
	{
		if (!isset($this->_uri)) {
			return false;
		}
		
		$id = get_class($plugin);
		if (isset($this->_lastMatch[$id]) && true == $this->_lastMatch[$id]) {
			return true;
		} elseif (isset($this->_lastMatch[$id]) && false == $this->_lastMatch[$id]) {
			return false;
		}
		
		$pathSegments = $this->_uri->getAllPathSegments();
		foreach ($plugin->getRoutes() as $route) {
			$wildcard = $route->getWildcard();
			
			if ($route->getMethod() != $this->_requestMethod) {
				$this->_lastMatch[$id] = false;
				return false;
			}
			
			foreach($route->getAllSegments() as $routeSegment) {
				$uriSegment = array_shift($pathSegments);
				if (strcasecmp($routeSegment, $uriSegment) == 0 || 
					strcasecmp($routeSegment, $wildcard) == 0) {
					continue;
				} else {
					$this->_lastMatch[$id] = false;
					return false;
				}
			}
		}
		$this->_lastMatch[$id] = true;
		return true;
	}
	
	private function _loadPluginFile($path)
	{ 
		require $path;
		$declaredClasses = get_declared_classes();
		$className = array_pop($declaredClasses);
		unset($declaredClasses);
		return new $className;
	}
}