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

namespace Mic\Representations\Request;

use Mic\Interfaces\Filter;
use Mic\Interfaces\Representation;

class FormUrlEncoded implements Representation
{
	protected $_data;
	
	protected $_parsedData;
	
	protected $_size;
		
	public function __construct($data, $size)
	{
		$this->_data = $data;
		$this->_size = $size;
	}
	
	public function getMediaType()
	{
		return 'application/x-www-form-urlencoded';
	}
	
	public function getData()
	{
		return $this->_data;
	}
	
	public function getParsedData()
	{
		
	}
	
	public function getSize()
	{
		return $this->_size;
	}
}