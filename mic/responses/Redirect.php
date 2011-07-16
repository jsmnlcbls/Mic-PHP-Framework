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

namespace Mic\Responses;

use Mic\Library\DefaultResponse;
use \InvalidArgumentException;

class Redirect extends DefaultResponse
{
	const 
	
	MOVED_PERMANENTLY = 301,
			
	FOUND = 302,
			
	SEE_OTHER = 303,
			
	TEMPORARY_REDIRECT = 307;
	
	public function __construct($statusCode)
	{
		switch($statusCode) {
			//cascades are intentional
			case self::MOVED_PERMANENTLY:
			case self::FOUND:
			case self::SEE_OTHER:
			case self::TEMPORARY_REDIRECT:
				parent::__construct($statusCode);
				break;
			default:
				throw new InvalidArgumentException("Invalid redirect status code.");
		}
		
		parent::__construct($statusCode);
	}
}