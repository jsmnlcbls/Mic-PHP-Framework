<?php
/**
 * Copyright 2010, Jose Manuel A. Cabiles III
 * 
 * This file is part of Mic Framework.
 * 
 * Mic Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Mic Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Mic Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mic\Library;

use Mic\Settings;
use Mic\Interfaces\PluginManager;
use Mic\Interfaces\Cache;
use Mic\Interfaces\ExceptionHandler;
use Mic\Interfaces\Plugin;
use Mic\Interfaces\Response;
use Mic\Interfaces\Resource;
use Mic\Interfaces\Request;
use Mic\Interfaces\ResourceMap;
use Mic\Exceptions\ResourceNotFoundException;
use Mic\Exceptions\InvalidStateException;

use \InvalidArgumentException;
use \UnexpectedValueException;
use \Exception;

class System
{
	protected $_characterSet = 'utf-8';
	
	/**
	 * Holds an instance of the resource map
	 * @var ResourceMap
	 */
	protected $_resourceMap;
	
	/**
	 * Holds an instance of the request
	 * @var Request
	 */
	protected $_request;
	
	/**
	 * Holds an instance of the response
	 * @var Response
	 */
	protected $_response;
	
        /**
         * Holds an instance of the resource
         * @var Resource
         */
	protected $_resource;
	
	/**
	 * Holds an instance of the exception handler
	 * @var ExceptionHandler
	 */
	protected $_exceptionHandler;
		
	/**
	 * Holds an instance of a cache implementation
	 * @var Cache
	 */
	protected $_cache;
	
	/**
	 * Holds the global settings for the framework
	 * @var Settings
	 */
	protected $_settings;
	
	/**
	 * Holds an instance of the plugin manager
	 * @var PluginManager
	 */
	protected $_pluginManager;
		
	/**
	 * Holds the sole instance of this class
	 * @var System
	 */
	private static $_instance;
	
	/**
	 * Holds the current state of system execution
	 * @var String
	 */
	private $_currentState;
	
	/**
	 * Holds the name of the next state that will be executed
	 * @var String
	 */
	private $_nextState;
	
	/**
	 * Holds the mapping of the state name to the class method for execution
	 * @var Array
	 */
	private $_stateMethod;

	/**
	 * Holds the method name for the starting state
	 * @var String
	 */
	private $_startState = 'Start';
	
	/**
	 * Holds the method name for the routing state
	 * @var String
	 */
	private $_routeState = 'Route';
	
	/**
	 * Holds the method name for the pre-dispatch state
	 * @var String
	 */
	private $_preDispatchState = 'Pre-Dispatch';
	
	/**
	 * Holds the method name for the dispatch state
	 * @var String
	 */
	private $_dispatchState = 'Dispatch';
	
	/**
	 * Holds the method name for the post dispatch state
	 * @var String
	 */
	private $_postDispatchState = 'Post-Dispatch';
	
	/**
	 * Holds the method name for the output state
	 * @var String
	 */
	private $_outputState = 'Output';
	
	/**
	 * Holds the method name for the end state
	 * @var String
	 */
	private $_endState = 'End';
	
	/**
	 * Holds the resources registered via this class
	 * @var Array
	 */
	private $_resourceQueue;
	
	/**
	 * Holds the plugins registered via this class
	 * @var Array
	 */
	private $_pluginQueue;
	
	/**
	 * Flag variable for determining whether the run method has been invoked
	 * @var Boolean
	 */
	private $_isRunning;
	
	/**
	 * Private constructor
	 * @return System
	 */
	private function __construct(Settings $settings)
	{
		$this->_settings = $settings;
                
        //populate this array with the method names to be invoked
        //for each corresponding state
		$this->_stateMethod = array($this->_startState => '_startState',
                                    $this->_routeState => '_routeState',
                                    $this->_preDispatchState => '_preDispatchState',
                                    $this->_dispatchState => '_dispatchState',
                                    $this->_postDispatchState => '_postDispatchState',
                                    $this->_outputState => '_outputState',
                                    $this->_endState => '_endState');
                
		$this->_isRunning = false;
		$this->_resetToStartState();
		try {
			$this->_initializeCharacterSet();
			$this->_intializeAutoloader();
		} catch (Exception $exception) {
			$this->_getRequest();
			$this->_exceptionState($exception);
		}
	}

	/**
	 * Returns the sole instance of this class 
	 * @return System
	 */
	public static function getInstance(Settings $settings = null)
	{		
		if (!isset(static::$_instance)) {
			if (null == $settings) {
				$settings = new Settings();
			}
			static::$_instance = new System($settings);
		}
		return static::$_instance;
	}
	
	protected function _startState()
	{
		$this->_isRunning = true;
		$this->_currentState = $this->_startState;
		$this->_nextState = $this->_routeState;
		$this->_getPluginManager();
		$this->_getRequest();
		
		$this->_pluginManager->setRequest($this->_request);
		$this->_invokePlugins('onStart', $this->_request);
	}
	
	protected function _routeState()
	{
		$this->_currentState = $this->_routeState;
		$this->_nextState = $this->_preDispatchState;
		$this->_getResourceMap();
		$this->_insertRegisteredResources();
		$uri = $this->_request->getUri();
		$resource = $this->_resourceMap->getResource($uri);	
		
		if ( !($resource instanceof Resource)) {
			throw new UnexpectedValueException("Invalid resource object"); 
		}
		$this->_resource = $resource;
	}
	
	protected function _preDispatchState()
	{
		$this->_currentState = $this->_preDispatchState;
		$this->_nextState = $this->_dispatchState;
		$this->_invokePlugins('preDispatch', $this->_request);
	}
	
	protected function _dispatchState()
	{
		$this->_currentState = $this->_dispatchState;
		$this->_nextState = $this->_postDispatchState;
		$method = $this->_request->getMethod();
		$response = $this->_resource->$method($this->_request);
		
		if (!($response instanceof Response)) {
			throw new UnexpectedValueException("Invalid response object");
		}
		$this->_response = $response;
	}
	
	protected function _postDispatchState()
	{
		$this->_currentState = $this->_postDispatchState;
		$this->_nextState = $this->_outputState;
		$this->_invokePlugins('postDispatch', $this->_response);
	}
	
	protected function _outputState()
	{
		$this->_currentState = $this->_outputState;
		$this->_nextState = $this->_endState;
		$type = $this->_settings->getDefaultContentType();
		$header = DefaultResponse::HEADER_CONTENT_TYPE;
		if ($this->_response->headerExists($header)) {
			$type = $this->_response->getHeader($header);
		}
		
		if (false === stripos($type, 'charset')) {
			$type .= ';charset=' . $this->_characterSet;	
		}
	
		$this->_response->setHeader($header, $type);
		$this->_response->output();
	}
	
	protected function _endState()
	{
		$this->_currentState = $this->_endState;
		$this->_invokePlugins('onEnd', $this->_response);
		$this->_resetToStartState();
		$this->_isRunning = false;
	}
	
	protected function _exceptionState(Exception $exception)
	{
		if ($this->_settings->isExceptionThrown()) {
			$this->_resetToStartState();
			$this->_isRunning = false;
			throw $exception;	
		}
		$handler = $this->_getExceptionHandler();
		$this->_invokePlugins('onException', $exception);
		$response = $handler->handle($exception, $this->_request);
		$response->output();
		$this->_resetToStartState();
		$this->_isRunning = false;
	}
	/*
	public function registerResource(Resource $resource, Route $route)
	{
		if ($this->_currentState != $this->_startState) {
			throw new InvalidStateException("Resource must be registered at the start state.");	
		}
		$this->_resourceQueue[] = array('resource' => $resource, 
										'route' => $route);
	}
	
	public function registerPlugin(Plugin $plugin)
	{
		if ($this->_currentState != $this->_startState) {
			throw new InvalidStateException("Plugin must be registered at the start state.");	
		}
		$this->_pluginQueue[] = $plugin;
	}
	*/	
	public function run()
	{
		if ($this->_isRunning) {
			throw new InvalidStateException("System is already running.");
		}
		
		try {
			$this->_resetToStartState();
			do {
				$method = $this->_stateMethod[$this->_nextState];
				$this->{$method}();
			} while($this->_nextState != $this->_endState);
			$this->_endState();
		} catch (Exception $exception) {
			$this->_exceptionState($exception);
		}
	}
		
	/**
	 * Set the resource map to be used
	 * @param ResourceMap $resourceMap
	 */
	public function setResourceMap(ResourceMap $resourceMap)
	{
		if ($this->_currentState != $this->_startState) {
			$message = 'Resource map must be set at the start state.';
			throw $this->_createInvalidStateException($message);
		}
		$this->_resourceMap = $resourceMap;
	}
	
	/**
	 * Set the request that will be processed
	 * @param Request $request
	 */
	public function setRequest(Request $request)
	{
		if ($this->_currentState != $this->_startState) {
			$message = 'Request must be set at the start state.';
			throw $this->_createInvalidStateException($message);
		}
		$this->_request = $request;	
	}
	
	/**
	 * Dispatch a resource immediately.
	 * @param Resource $resource
	 * @throws LogicException if the system is neither in the start, route or 
	 * pre-dispatch state.
	 */
	public function dispatchResource(Resource $resource)
	{
		if ($this->_currentState == $this->_startState ||
			$this->_currentState == $this->_routeState ||
			$this->_currentState == $this->_preDispatchState) {
			
			$this->_resource = $resource;
			$this->_nextState = $this->_dispatchState;
		} else {
			$message = 'Resource can only be set at the start, route and pre-dispatch state.';
			throw $this->_createInvalidStateException($message);
		}
	}
	
	/**
	 * Output a response immediately. 
	 * @param Response $response
	 * @throws InvalidStateException if the system has already reached the end state
	 */
	public function outputResponse(Response $response)
	{
		if ($this->_currentState == $this->_endState) {
			$message = 'Response can no longer be set at the end state.';
			throw $this->_createInvalidStateException($message);
		}
		$this->_response = $response;
		$this->_nextState = $this->_outputState;
	}
	
	/**
	 * Set the exception handler
	 * @param ExceptionHandler $handler
	 */
	public function setExceptionHandler(ExceptionHandler $handler)
	{
		$this->_exceptionHandler = $handler;
	}
	
	/**
	 * Set the cache
	 * @param Cache $cache
	 */
	public function setCache(Cache $cache)
	{
		$this->_cache = $cache;
	}
	
	public function setPluginManager(PluginManager $manager)
	{
		$this->_pluginManager = $manager;
	}
	
	public function setConfiguration(Settings $settings)
	{
		$this->_settings = $settings;
	}
	
	private function _intializeAutoloader()
	{
		if ($this->_settings->isFrameworkLoaderEnabled()) {
			require __DIR__ . DIRECTORY_SEPARATOR . 'Loader.php';
			Loader::registerFrameworkLoader($this->_settings->getMicDirectory());
		}
		
		if ($this->_settings->isApplicationLoaderEnabled()) {
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'Loader.php';
			Loader::registerApplicationLoader($this->_settings->getApplicationDirectory());
		}
	}
	
	private function _initializeCharacterSet()
	{
		mb_internal_encoding($this->_characterSet);
		mb_regex_encoding($this->_characterSet);
	}
	
	/**
	 * Insert any plugins registered via this class to the plugin manager
	 */
	private function _insertRegisteredPlugins()
	{
		if (!empty($this->_pluginQueue)) {
			foreach ($this->_pluginQueue as $plugin) {
				$this->_pluginManager->register($plugin);
			}
		}
	}
	
	/**
	 *	Insert any resource registered via this class to the resource map
	 */
	private function _insertRegisteredResources()
	{
		if (!empty($this->_resourceQueue)) {
			foreach ($this->_resourceQueue as $value) {
				$resource = $value['resource'];
				$route = $value['route'];
				$this->_resourceMap->addResource($resource, $route);
			}
		}
	}
	
	private function _createInvalidStateException($message)
	{
		$exception = new InvalidStateException($message);
		$exception->setCurrentState($this->_currentState);
		return $exception;
	}
	
	private function _resetToStartState()
	{
		$this->_currentState = $this->_startState;
		$this->_nextState = $this->_startState;
	}
	
	protected function _invokePlugins($method, $arguments = array())
	{
		if (!isset($this->_pluginManager) && $this->_settings->isPluginEnabled()) {
			$this->_pluginManager = $this->_getPluginManager();
		}
		
		if (isset($this->_pluginManager)) {
			$this->_pluginManager->invoke($method, $arguments);
		}
	}
	
	/**
	 * Returns the resource map. Defaults to FilesystemMap
	 * @return ResourceMap
	 */
	protected function _getResourceMap()
	{		
		if (!isset($this->_resourceMap)) {
			$directory = $this->_settings->getResourceDirectory();
			$wildcardDirectory = $this->_settings->getResourceWildcardDirectory();
			$wildcardFilename = $this->_settings->getResourceWildcardFilename();
			$fileExtension = $this->_settings->getResourceFileExtension();
			
			$this->_resourceMap = new FilesystemMap($directory);
			$this->_resourceMap->setWildcardDirectory($wildcardDirectory);
			$this->_resourceMap->setWildcardFilename($wildcardFilename);
			$this->_resourceMap->setFileExtension($fileExtension);
			
			if ($this->_settings->isCacheEnabled()) {
				$this->_resourceMap->setCache($this->_getCache());
			}
		}
		return $this->_resourceMap;
	}
	
	/**
	 * Returns an instance of request to be used for processing. If no request
	 * instance has been set, defaults to \Mic\Library\Request
	 * @return \Mic\Interfaces\Request
	 */
	protected function _getRequest()
	{
		if (!isset($this->_request)) {
			$this->_request = new DefaultRequest();
			$basePath = $this->_settings->getBasePath();
			if (!empty($basePath)) {
				$uri = new DefaultUri;
				$uri->setBasePath($basePath);
				$this->_request->setUri($uri);
			}
		}
		return $this->_request;
	}
	
	protected function _getResponse()
	{
		
	}
	
	/**
	 * Returns the exception handler that has been set. If no handler has been
	 * set, defaults to \Mic\Library\Catcher.
	 * @return \Mic\Interfaces\ExceptionHandler
	 */
	protected function _getExceptionHandler()
	{
		if (!isset($this->_exceptionHandler)) {
			$handler = new DefaultExceptionHandler();
			$handler->set404Template($this->_settings->get404ErrorTemplate());
			$handler->set405Template($this->_settings->get405ErrorTemplate());
			$handler->set500Template($this->_settings->get500ErrorTemplate());
			$this->_exceptionHandler = $handler;
		}
		return $this->_exceptionHandler;
	}
	
	protected function _getCache()
	{
		if (!isset($this->_cache) && $this->_settings->isCacheEnabled()) {
			$this->_cache = new FileCache();
		}
		return $this->_cache;
	}
	
	protected function _getPluginManager()
	{
		if (!isset($this->_pluginManager)) {
			$micPlugins = $this->_settings->getMicDirectory() 
						. DIRECTORY_SEPARATOR . 'plugins';
			$appPlugins = $this->_settings->getApplicationDirectory()
						. DIRECTORY_SEPARATOR . 'plugins';
			$path = implode(PATH_SEPARATOR, array($micPlugins, $appPlugins));
			$this->_pluginManager = new DefaultPluginManager($path);
			
			if ($this->_settings->isCacheEnabled()) {
				$this->_pluginManager->setCache($this->_getCache());	
			}
			return $this->_pluginManager;
		}
	}
}