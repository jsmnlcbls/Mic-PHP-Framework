<?php

namespace Mic\Library;

use Mic\Interfaces\Route;
use Mic\Library\DefaultRequest;

use InvalidArgumentException;

class DefaultRoute implements Route
{
	const GET = DefaultRequest::METHOD_GET;

	const DELETE = DefaultRequest::METHOD_DELETE;
	
	const POST = DefaultRequest::METHOD_POST;
	
	const PUT = DefaultRequest::METHOD_PUT;
	
	protected $_route;
	
	protected $_method;
	
	protected $_wildcard;
	
	private $_segments;
	
	private $_segmentCount;
	
	public function __construct($route, $method = self::GET, $wildcard = "*")
	{
		if (!is_string($route)) {
			throw new InvalidArgumentException("Path must be string.");
		}
		
		if (!is_string($method) && !is_array($method)) {
			throw new InvalidArgumentException("Method must be string or array.");
		}
		
		if (!is_string($wildcard)) {
			throw new InvalidArgumentException("Method must be string.");
		}
		
		$this->_route = $route;
		$this->_method = $method;
		$this->_wildcard = $wildcard;
	}
	
	public function getMethod()
	{
		return $this->_method;
	}
	
	public function getRoute()
	{
		return $this->_route;
	}

	public function getAllSegments()
	{
		$this->_initializeSegments();
		return $this->_segments;
	}
	
	public function getSegment($offset)
	{
		$this->_initializeSegments();
		$offset = (int) $offset;
		if ($offset < 0 || $offset > ($this->_segmentCount - 1)) {
			throw new OutOfRangeException("Offset is out of range.");
		}
		
		return $this->_segments[$offset];
	}
	
	public function getWildcard()
	{
		return $this->_wildcard;
	}
	
	private function _initializeSegments()
	{
		if (!isset($this->_segments)) {
			$route = ltrim($this->_route, '/');
			$this->_segments = mb_split("/", $route);
			$this->_segmentCount = count($this->_segments);
		}
	}
}