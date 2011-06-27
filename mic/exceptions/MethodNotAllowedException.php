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

namespace Mic\Exceptions;

use BadMethodCallException;

class MethodNotAllowedException extends BadMethodCallException 
								implements MicException
{
	protected $_allowedMethod;
	
	protected $_method;
	
	public function setMethod($method)
	{
		if (!is_string($method) || empty($method)) {
			throw new \InvalidArgumentException('Invalid method name');
		}
		
		$this->_method = $method;
	}
	
	public function addAllowedMethod($method)
	{
		if (!is_string($method) || empty($method)) {
			throw new \InvalidArgumentException('Invalid method name');
		}
		
		$this->_allowedMethod[] = $method;
	}
	
	public function getAllowedMethod()
	{
		if (!isset($this->_allowedMethod)) {
			$this->_allowedMethod = array();
		}
		return $this->_allowedMethod;
	}
	
	public function getMethod()
	{
		return $this->_method;
	}
}