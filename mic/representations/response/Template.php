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

namespace Mic\Representations\Response;

use Mic\Interfaces\Representation;
use Mic\Interfaces\Filter;

use UnexpectedValueException;

class Template implements Representation
{
	protected $_filename;

	protected $_mediaType;
	
	protected $_content;
	
	protected $_size;

	public function __construct($filename)
	{
		$this->_filename = (string) $filename;	
		$this->_mediaType = 'text/html';
	}
	
	public function set($key, $value)
	{
		$this->_content[(string) $key] = $value;
	}
	
	public function get($key)
	{
		if (!is_string($key)) {
			throw new InvalidArgumentException("Key must be string.");
		}
		
		if (!array_key_exists($key, $this->_content)) {
			throw new UnexpectedValueException("Value not found for $key");
		}
		
		return $this->_content[$key];
	}
	
	public function getSize()
	{
		$size = strlen($this->getData());
		return $size;
	}
	
	public function getMediaType()
	{
		return $this->_mediaType;
	}

	public function getData()
	{
		if (!is_readable($this->_filename)) {
			throw new UnexpectedValueException("Template file {$this->_filename} is unreadable.");
		}
		
		ob_start();
		if (count($this->_content) > 0) {
			extract($this->_content, EXTR_OVERWRITE);
		}
		require $this->_filename;
		$data = ob_get_contents();
		ob_end_clean();
		
		return $data;
	}
}