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
 * FileCache is an implementation of Cache that stores key value pairs in a 
 * file in the temporary directory of the operating system.
 * @author enilehcim
 *
 */

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