<?php

namespace Mic\Library;

use Mic\Interfaces\Cache;
use InvalidArgumentException;
use UnexpectedValueException;

class FileCache implements Cache
{
	protected $_file;
	
	protected $_cache;
	
	private $_isModified;
	
	public function __construct($filename = null)
	{
		$this->_file = $filename;
		if (null == $filename || !is_string($filename)) {
			$filename = 'mic-serialized-file-cache.php';
			$this->_file = sys_get_temp_dir() . $filename;
		}
		
		if (is_readable($this->_file)) {
			$contents = file_get_contents($this->_file);
			$this->_cache = unserialize($contents);
		}		
		$this->_isModified = false;
	}
		
	public function get($key)
	{
		$this->_validateKey($key);
		
		if (isset($this->_cache[$key])) {
			return $this->_cache[$key];	
		}
		
		throw new UnexpectedValueException("Cache entry not found.");
	}
	
	public function store($key, $value)
	{
		$this->_validateKey($key);
		$this->_cache[$key] = $value;
		$this->_isModified = true;
	}
	
	public function exists($key)
	{
		$this->_validateKey($key);
		
		if (isset($this->_cache[$key])) {
			return true;
		}
		return false;
	}
	
	public function clear($key)
	{
		$this->_validateKey($key);
		unset($this->_cache[$key]);
		$this->_isModified = true;
	}
	
	public function clearAll()
	{
		$this->_cache = null;
		$this->_isModified = true;
	}
	
	/**
	 *	Write the current cache values to file. 
	 */
	public function synchronize ()
	{
		$tempFilename = tempnam(sys_get_temp_dir(), "mic");
		if (false !== $tempFilename) {
			$data = serialize($this->_cache);
			$result = file_put_contents($tempFilename, $data);
			if (false !== $result) {
				//http://bugs.php.net/bug.php?id=44805
				rename($tempFilename, $this->_file);
				return;
			}
		}
		throw new UnexpectedValueException("Unable to write cache values to $this->_file.");
	}
	
	public function __destruct()
	{
		if (!$this->_isModified) {
			return;
		}
		$this->synchronize();
	}
	
	private function _validateKey($key)
	{
		if (!is_string($key) || empty($key)) {
			throw new InvalidArgumentException("Invalid cache key");
		}
	}
}