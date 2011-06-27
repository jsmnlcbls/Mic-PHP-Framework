<?php

namespace Mic\Library;

use \Mic\Interfaces\Uri;
use \InvalidArgumentException;
use \UnexpectedValueException;
use \OutOfRangeException;

class DefaultUri implements Uri
{
	const SCHEME_HTTP = 'http';
	
	const SCHEME_HTTPS = 'https';
	
	protected $_scheme;
	
	protected $_host;
	
	protected $_port;

	protected $_path;
	
	protected $_query;
	
	protected $_fragment;
	
	private $_segmentCount;
	
	private $_segments;
	
	private $_queryVariables;
	
	private $_basePath;
	
	public function __construct($uri = null, $basePath = '')
	{
		if (null !== $uri && (!is_string($uri) || empty($uri))) {
			throw new InvalidArgumentException("Uri must be non-empty string.");
		}
		
		if (!is_string($basePath)) {
			throw new InvalidArgumentException("Invalid base path.");
		}
		
		if (null !== $uri) {
			$this->_initializeUri($uri);
		}
		
		$this->_basePath = $basePath;
	}
	
	public function getScheme()
	{
		if (!isset($this->_scheme)) {	
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
				$this->_scheme = self::SCHEME_HTTPS;	
			} else {
				$this->_scheme = self::SCHEME_HTTP;
			}
		}
		return $this->_scheme;
	}
	
	public function getHost()
	{
		if (!isset($this->_host)) {
			if (!empty($_SERVER['HTTP_HOST'])) {
				$this->_host = $_SERVER['HTTP_HOST'];
			} else {
				throw new UnexpectedValueException("No host name found.");
			}
		}
		return $this->_host;
	}
	
	public function getPort()
	{
		if (!isset($this->_port)) {
			if (!empty($_SERVER['SERVER_PORT'])) {
				$this->_port = $_SERVER['SERVER_PORT'];
			} else {
				throw new UnexpectedValueException("No server port found.");
			}
		}
		return $this->_port;	
	}
	
	public function getPath()
	{
		if (!isset($this->_path)) {
			$this->_path = parse_url($this->_getRequestUri(), PHP_URL_PATH);
			$this->_path = $this->_stripBasePath($this->_path, $this->_basePath);
		}
		return $this->_path;
	}
	
	public function getQuery()
	{
		if (!isset($this->_query)) {
			$this->_query = parse_url($this->_getRequestUri(), PHP_URL_QUERY);
		}
		return $this->_query;
	}
	
	public function getFragment()
	{
		if (!isset($this->_fragment)) {
			$this->_fragment = '';
		}
		return $this->_fragment;
	}
	
	public function getBasePath()
	{
		if (!isset($this->_basePath)) {
			$this->_basePath = '';
		}
		return $this->_basePath;
	}
	
	public function getAllPathSegments()
	{
		$this->_initializeSegments();
		return $this->_segments;
	}
	
	public function getPathSegment($offset)
	{
		$this->_initializeSegments();
		if ($offset < 0 || $offset > ($this->_segmentCount - 1)) {
			throw new OutOfRangeException("Segment offset is out of range.");
		}
		
		return $this->_segments[$offset];
	}
	
	public function getPathSegmentAfter($segment)
	{
		$segment = (string) $segment;
		if (empty($segment)) {
			throw new InvalidArgumentException("Invalid segment");
		}
		
		$this->_initializeSegments();
		$index = array_search($segment, $this->_segments);
		
		if (false === $index) {
			throw new UnexpectedValueException("Path segment after '$segment' not found.");
		}
		
		return $this->_segments[$index+1];
	}
	
	public function getAllQueryValues()
	{
		$this->_initializeQueryVariables();
		return $this->_queryVariables;
	}
	
	public function getQueryValue($key)
	{
		$this->_initializeQueryVariables();
		
		if (!isset($this->_queryVariables[$key])) {
			throw new UnexpectedValueException("No query value found for '$key'");
		}
		
		return $this->_queryVariables[$key];
	}
	
	public function setScheme($scheme)
	{
		if (!is_string($scheme) || empty($scheme)) {
			throw new InvalidArgumentException("Invalid scheme.");
		}
		$this->_scheme = $scheme;
	}
	
	public function setHost($host)
	{
		//TODO: add a host name validator
		if (!is_string($host) || empty($host)) {
			throw new InvalidArgumentException("Invalid host.");
		}
		$this->_host = $host;
	}
	
	public function setPort($port)
	{
		$this->_port = (int) $port;
	}
	
	public function setPath($path)
	{
		if (!is_string($path) || empty($path)) {
			throw new InvalidArgumentException("Invalid path.");
		}
		if (isset($this->_basePath)) {
			$this->_path = $this->_stripBasePath($path, $this->_basePath);
		} else {
			$this->_path = (string) $path;
		}
		$this->_initializeSegments(true);
	}
	
	public function setQuery($query)
	{
		if (!is_string($query)) {
			throw new InvalidArgumentException("Invalid query.");
		}
		$this->_query = $query;
		$this->_initializeQueryVariables(true);
	}
	
	public function setFragment($fragment)
	{
		$this->_fragment = (string) $fragment;
	}
	
	public function setBasePath($basePath)
	{
		$this->_basePath = (string) $basePath;
	}
		
	public function buildRelativeUri()
	{
		$basePath 	= $this->getBasePath();
		$path 		= $this->getPath();
		$query  	= $this->getQuery();
		$fragment 	= $this->getFragment();
		
		$uri = $basePath;
		if (!empty($path)) {
			$uri .= "{$path}";
		}
		if (!empty($query)) {
			$uri .= "?{$query}";
		}
		if (!empty($fragment)) {
			$uri .= "#{$fragment}";
		}
		return $uri;
	}
	
	public function buildAbsoluteUri()
	{
		$scheme		= 	$this->getScheme();
		$host 		= 	$this->getHost();
		$path 		=	$this->getPath();
		$query		=	$this->getQuery();
		$fragment	=	$this->getFragment();
		
		$relativeUri = $this->buildRelativeUri();
		
		return "$scheme://{$host}{$relativeUri}";
	}
	
	public function __toString()
	{
		return $this->buildAbsoluteUri();
	}
	
	private function _getRequestUri()
	{
		//From Drupal
		if (isset($_SERVER['REQUEST_URI'])) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			if (isset($_SERVER['argv'])) {
				$uri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['argv'][0];
			} elseif (isset($_SERVER['QUERY_STRING'])) {
		    	$uri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
		    } else {
		      $uri = $_SERVER['SCRIPT_NAME'];
		    }
		}
		$uri = '/'. ltrim($uri, '/');
		
		return $uri;
	}
	
	private function _initializeSegments($override = false)
	{
		if (!isset($this->_segments) || $override) {
			$path = $this->_stripLeadingSlash($this->getPath());
			$this->_segments = mb_split("/", $path);
			$this->_segmentCount = count($this->_segments);
		}
	}
	
	private function _initializeUri($uri)
	{
		$uriParts = parse_url($uri);	
		if (isset($uriParts['scheme'])) {
			$this->_scheme = $uriParts['scheme'];
		}
		
		if (isset($uriParts['host'])) {
			$this->_host = $uriParts['host'];
		}
		
		if (isset($uriParts['port'])) {
			$this->_port = $uriParts['port'];
		}
		
		$this->_path = '';
		if (isset($uriParts['path'])) {
			$this->_path = $uriParts['path'];
		}
		
		$this->_query = '';
		if (isset($uriParts['query'])) {
			$this->_query = $uriParts['query'];	
		}
		
		$this->_fragment = '';
		if (isset($uriParts['fragment'])) {
			$this->_fragment = $uriParts['fragment'];
		}
	}
	
	private function _initializeQueryVariables($override = false)
	{
		if (!isset($this->_queryVariables) || $override) {
			parse_str($this->_query, $this->_queryVariables);
		}
	}
	
	private function _stripTrailingSlash($path)
	{
		$lastPosition = mb_strlen($path) - 1;
		if ($lastPosition === mb_strrpos($path, "/")) {
			return mb_substr($path, 1);
		}
		return $path;		
	}
	
	private function _stripLeadingSlash($path)
	{
		if (0 === mb_strpos($path, "/")) {
			return mb_substr($path, 1);
		}
		return $path;
	}
	
	private function _stripBasePath($path, $basePath)
	{
		
		$basePath = mb_ereg_replace("/", "\/", $basePath);
		$pattern = "^" . $basePath;
		$newUri = mb_ereg_replace($pattern, "", $path, 1);
		return $newUri;
	}
	
}
