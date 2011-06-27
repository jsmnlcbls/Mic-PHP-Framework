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

/**
 * FilesystemMap is an implementation of a resource map that allows resources
 * to be mapped using the filesytem directory and files to represent the URI
 * path. It also allows specially named directories and files to act as 
 * wildcards when interpreting the path.
 * 
 * @author enilehcim
 */

namespace Mic\Library;

use Mic\Interfaces\Cache;
use Mic\Interfaces\ResourceMap;
use Mic\Interfaces\Uri;
use Mic\Interfaces\Resource;
use Mic\Interfaces\Route;
use Mic\Exceptions\ResourceNotFoundException;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use InvalidArgumentException;
use UnexpectedValueException;
use Exception;
use DirectoryIterator;

class FilesystemMap implements ResourceMap
{
	protected $_rootDirectory;

	protected $_resourceMap;
	
	protected $_defaultResource;
		
	protected $_routes;
	
	protected $_fileExtension;
	
	protected $_wildcardDirectory;
	
	protected $_wildcardFilename;
	
	protected $_cache;

	private $_wildcard;
	
	private $_routeWildcard;
	
	private $_fileIndex;
	
	private $_classIndex;
	
	private $_resourceCache;
	
	public function __construct($rootDirectory = null)
	{
		if (!is_null($rootDirectory)) {
			$this->setRootDirectory($rootDirectory);
		}
		
		$this->_fileIndex = '@file';
		$this->_classIndex = '@class';
		$this->_wildcard = '/*/';
	}
	
	public function getResource(Uri $uri)
	{
		$path = $uri->getPath();
		//check if the method resourceExists was called first with the same uri
		//which could mean that the resource was already cached if it was found
		if ($this->_isCached($path)) {
			return $this->_getFromCache($path);
		}
		
		$this->_initializeMap();
		$resource = $this->_getClosestResource($uri->getAllPathSegments());
		if (!($resource  instanceof Resource)) {
			$message = "Resource identified by $path does not exists.";
			$notFound = new ResourceNotFoundException($message, 404);
			$notFound->setUri($uri);
			throw $notFound;
		}
		
		return $resource;
	}
	
	public function resourceExists(Uri $uri)
	{
		$this->_initializeMap();
		$path = $uri->getPath();
		$resource = $this->_getClosestResource($uri->getAllPathSegments());
		if (null !== $resource) {
			$this->_cacheResource($path, $resource);
			return true;
		}
		return false;
	}
	
	public function addResource(Resource $resource, Route $route)
	{
		$this->_routeWildcard = $route->getWildcard();
		$routeSegments = $route->getAllSegments();
		$this->_initializeMap();
	
		$className = get_class($resource);
		
		$ok = $this->_insertRoute($this->_resourceMap, $routeSegments, $className);
		
		if (!$ok) {
			throw new RouteInUseException($message, $code, $previous);
		}
	}
		
	public function routeExists(Route $route)
	{
		$this->_initializeMap();
		$map = $this->_resourceMap;
		$routeSegments = $route->getAllSegments();
		$lastSegment = array_pop($routeSegments);
		
		$routeWildcard = $route->getWildcard();
		foreach ($routeSegments as $value) {
			if ($value === $routeWildcard) {
				$value = $this->_wildcard;
			}
			if (!isset($map[$value])) {
				return false;
			}
			$map = $map[$value];
		}
		if (isset($map[$lastSegment][$this->_fileIndex]) || 
			isset($map[$lastSegment][$this->_classIndex])) {

			return true;
		}
	}
		
	public function setRootDirectory($directory)
	{
		if (!is_dir($directory)) {
			throw new InvalidArgumentException("Invalid directory '$directory'.");
		}
		$this->_rootDirectory = $directory;
	}
	
	public function setFileExtension($extension)
	{
		if (!is_string($extension) || empty($extension)) {
			throw new InvalidArgumentException("Invalid file extension.");
		}
		
		$this->_fileExtension = $extension;
		return $this;
	}
	
	public function setWildcardDirectory($name)
	{
		if (empty($name) || !is_string($name)) {
			throw new InvalidArgumentException("Invalid wildcard directory");
		}
		$this->_wildcardDirectory = $name;
	}
	
	public function setWildcardFilename($name)
	{
		if (empty($name) || !is_string($name)) {
			throw new InvalidArgumentException("Invalid wildcard filename");
		}
		$this->_wildcardFilename = $name;
	}
	
	public function setCache(Cache $cache)
	{
		$this->_cache = $cache;	
	}

	private function _initializeMap()
	{	
		if (isset($this->_resourceMap)) {
			return;
		}
		
		if (!isset($this->_rootDirectory)) {
			$this->_resourceMap = array();
			return;
		}
		
		$cacheKey = "cache-{$this->_rootDirectory}";
		if (isset($this->_cache) && $this->_cache->exists($cacheKey)) {
			$this->_resourceMap = $this->_cache->get($cacheKey);
			return;
		}
		
		$this->_getWildcardDirectory();
		$this->_getWildcardFilename();
		$this->_getFileExtension();
		$this->_resourceMap = $this->_generateDirectoryMap($this->_rootDirectory);
		
		if (isset($this->_cache) && !$this->_cache->exists($cacheKey)) {
			$this->_cache->store($cacheKey, $this->_resourceMap);
		}
	}
	
	private function _insertRoute(&$map, &$route, &$className)
	{
		$current = array_shift($route);
		if ($current === $this->_routeWildcard) {
			$current = $this->_wildcard;
		}
		if (!isset($map[$current])) {
			$map[$current] = array();
			if (empty($route)) {
				$map[$current] = array($this->_classIndex => $className);
				return true;
			}
		} elseif (isset($map[$current]) && empty($route)) {
			if (isset($map[$current][$this->_classIndex]) ||
				isset($map[$current][$this->_fileIndex])) {
				
				return false;
			}
		}
		
		return $this->_insertRoute($map[$current], $route, $className);
	}
	
	private function _generateDirectoryMap($path)
	{
		$directory = new RecursiveDirectoryIterator($path);
		$map = array();
		$suffix = '.' . $this->_fileExtension;
		$fileList = new RecursiveIteratorIterator($directory);
		foreach ($fileList as $file) {
			$extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
			if ($extension !== $this->_fileExtension) {
				continue;
			}
			$key = strtolower($file->getBasename($suffix));
			$fileMarker = array($this->_fileIndex => $file->getBasename());
			if ($this->_isWildcardFilename($key)) {
				$key = $this->_wildcard;
			}
			$fileMap = array($key => $fileMarker);
	
			$path = str_replace($this->_rootDirectory, "", $file->getPath());
			//php has no multibyte filename support until PHP 6 so ltrim suffices
			$path = ltrim($path, DIRECTORY_SEPARATOR);
			$segments = explode(DIRECTORY_SEPARATOR, $path);
			while (!empty($path) && !empty($segments)) {
				$key = strtolower(array_pop($segments));
				if ($this->_isWildcardDirectory($key)) {
					$key = $this->_wildcard;
				}
				$fileMap = array($key => $fileMap);
			}
			$map = array_merge_recursive($fileMap, $map);
		}
		return $map;
	}
	
	private function _generateSubdirectoryMap($directory)
	{

		return $map;
	}
	
	/**
	 * Generate a resource map using a path as the root directory. The map is a
	 * tree with the resource filenames as the leaf nodes. Implemented using
	 * an associative array with the directory names as array keys and a special
	 * array key for marking the leaf nodes
	 * @param String $path
	 * @return Array
	 */
	private function _generateDirectoryMapOld($path)
	{
		$directory = new DirectoryIterator($path);
		$map = array();
		
		$suffix = '.' . $this->_fileExtension;
		foreach ($directory as $value) {
			$key = $value->getBasename($suffix);
			$key = strtolower($key);
			if ($value->isDir() && !$value->isDot()) {
				$result = $this->_generateDirectoryMap($value->getPathname());
				
				if (0 == strcasecmp($key, $this->_wildcardDirectory)) {
					$key = $this->_wildcard;
				}
				
				//if a non empty directory, set it as an array key with
				//descendant map as the value
				if (!empty($result)) {
					$map[$key] = $result;
				}
			} elseif ($value->isFile()) {
				if (0 == strcasecmp($key, $this->_wildcardFilename)) {
					$key = $this->_wildcard;
				}
				//if a .php file, mark in the map as a file and store filename
				if (false !== strrpos($value->getBasename(), $suffix)) { 
					$map[$key][$this->_fileIndex] = $value->getFilename();
				}
			}
		}
		return $map;
	}
	
	private function _getClosestResource($pathSegments)
	{		
		$match = $this->_getClosestMatch($pathSegments);
		
		if (empty($match)) {
			return null;
		}
		
		if ($match['type'] == $this->_fileIndex) {
			$subdirectory = implode(DIRECTORY_SEPARATOR, $match['subDirectory']);
			$file = $subdirectory . DIRECTORY_SEPARATOR . $match['file'];
			return $this->_loadResourceFile($file);
		} elseif ($match['type'] == $this->_classIndex) {
			$className = $match['class'];
			return new $className;
		}
	}
	
	private function _getClosestMatch($pathSegments)
	{
		$map = $this->_resourceMap;
		$directory = array();
		$match = array();
		
		do {
			$segment = array_shift($pathSegments);
			
			if (isset($map[$segment])) {
				$match['subDirectory'] = $directory;
				if (isset($map[$segment][$this->_fileIndex])) {
					$match['file'] = $map[$segment][$this->_fileIndex];
					$match['type'] = $this->_fileIndex;
				} elseif (isset($map[$segment][$this->_classIndex])) {
					$match['class'] = $map[$segment][$this->_classIndex];
					$match['type'] = $this->_classIndex;
				}
				$directory[] = $segment;
				$map = $map[$segment];
			} elseif (isset($map[$this->_wildcard])) {
				$match['subDirectory'] = $directory;
				if (isset($map[$this->_wildcard][$this->_fileIndex])) {
					$match['file'] = $map[$this->_wildcard][$this->_fileIndex];
					$match['type'] = $this->_fileIndex;
				}
				$directory[] = $this->_wildcardDirectory;
				$map = $map[$this->_wildcard];
			} else {
				$match = null;
				break;
			}			
		} while(!empty($pathSegments) && !empty($map));

		return $match;
	}
	
	private function _loadResourceFile($file)
	{
		$resourceFile = $this->_rootDirectory . DIRECTORY_SEPARATOR . $file;
	
		require_once $resourceFile;
		$declaredClasses = get_declared_classes();
		$className = array_pop($declaredClasses);
		$class = new $className;
		return $class;	
	}
	
	private function _cacheResource($uri, $resource)
	{
		$this->_resourceCache['uri'] = $uri;
		$this->_resourceCache['resource'] = $resource;
	}
	
	private function _isCached($uri)
	{
		if ($this->_resourceCache['uri'] === $uri) {
			return true;
		}
		return false;
	}
	
	private function _getFromCache($uri)
	{
		if ($this->_resourceCache['uri'] === $uri) {
			return $this->_resourceCache['resource'];
		}
		
		throw new UnexpectedValueException("Resource not yet in cache");
	}
	
	private function _getFileExtension()
	{
		if (!isset($this->_fileExtension)) {
			$this->_fileExtension = 'php';
		}
		return $this->_fileExtension;
	}

	private function _getWildcardDirectory()
	{
		if (!isset($this->_wildcardDirectory)) {
			$this->_wildcardDirectory = '__Wildcard';
		}
		return $this->_wildcardDirectory;
	}
	
	private function _getWildcardFilename()
	{
		if (!isset($this->_wildcardFilename)) {
			$this->_wildcardFilename = '__Wildcard';
		}
		return $this->_wildcardFilename;
	}
	
	private function _isWildcardDirectory($directory)
	{
		if (0 == strcasecmp($this->_wildcardDirectory, $directory)) {
			return true;
		}
		return false;
	}
	
	private function _isWildcardFilename($filename)
	{
		if (0 == strcasecmp($this->_wildcardFilename, $filename)) {
			return true;
		}
		return false;
	}
};