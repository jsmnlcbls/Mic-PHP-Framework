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

namespace Mic\Representations;

use Mic\Interfaces\Representation;
use Mic\Interfaces\Filter;

class TextPlain implements Representation
{
	protected $_text;
	
	public function __construct($text)
	{
		$this->_text = $text;
	}
	
	public function getMediaType()
	{
		return 'text/plain';
	}
	
	public function getSize()
	{
		return strlen($this->_text);
	}
	
	public function getData()
	{
		return $this->_text;
	}
}