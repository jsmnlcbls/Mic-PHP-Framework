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

namespace Mic\Interfaces;

use Mic\Interfaces\Uri;
use Mic\Interfaces\Representation;

interface Request
{		
	/**
	 * Set the request uri
	 * @param String $uri
	 */
	public function setUri(Uri $uri);
	
	/**
	 * Set the request method
	 * 
	 * @param String $method
	 */
	public function setMethod($method);
	
	/**
	 * Set a header for this request
	 * @param String $name header name
	 * @param Mixed $value header value
	 */
	public function setHeader($name, $value);
	
	/**
	 * Set the representation for this resource
	 * @param RepresentationInterface $representation
	 */
	public function setRepresentation(Representation $representation);
	
	/**
	 * Returns the request uri
	 * 
	 * @return \Mic\Interfaces\Uri
	 */
	public function getUri();
	
	/**
	 * Returns the request method
	 * 
	 * @return String
	 */
	public function getMethod();
	
	/**
	 * Returns the header value
	 * 
	 * @return String
	 */
	public function getHeader($name);
	
	/**
	 * Returns the representation
	 * 
	 * @return RepresentationInterface
	 */
	public function getRepresentation();
	
	/**
	 * Check whether or not a header exists
	 * 
	 * @param String $header name of header
	 * @return Boolean
	 */
	public function headerExists($header);
}