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

interface Response
{
	/**
	 * Return the value for a given header name
	 * @param String $key
	 * @return String
	 */
	public function getHeader($name);
	
	/**
	 * Returns the representation for this response
	 * @return Mic\Interfaces\Representation
	 */
	public function getRepresentation();
	
	/**
	 * Returns the status code
	 * @return int
	 */
	public function getStatusCode();
	
	/**
	 * Check whether or not a header exists
	 * @return boolean
	 */
	public function headerExists($name);
	
	/**
	 * Set a response header
	 * @param String $name
	 * @param String $value
	 */
	public function setHeader($name, $value);
	
	/**
	 * Set the representation
	 * @param Mic\Interfaces\Representation $representation
	 */
	public function setRepresentation(Representation $representation);
	
	/**
	 * Set the status code
	 * @param int $statusCode
	 */
	public function setStatusCode($statusCode);
	
	/**
	 * Send the complete response back to the requesting client
	 */
	public function output();
}
