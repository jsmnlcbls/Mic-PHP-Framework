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

interface Resource
{
	/**
	 * Retrieve a representation of a resource identified by the request URI.
	 * 
	 * @param Mic\Interfaces\Request $request The request
	 * @return  Mic\Interfaces\Response
	 */
	public function get(Request $request);
	
	/**
	 * Create or modify the resource identified by the request URI using the
	 * contents of the request.
	 * 
	 * @param Mic\Interfaces\Request $request The request
	 * @return Mic\Interfaces\Response
	 */
	public function put(Request $request);
						
	/**
	 * Delete the resource identified by the request URI.
	 * 
	 * @param Mic\Interfaces\Request $request The request
	 * @return Mic\Interfaces\Response
	 */
	public function delete(Request $request);

	/**
	 * Accept the request contents as a new subordinate of the resource 
	 * identified by the request URI.
	 * 
	 * @param Mic\Interfaces\Request $request The request
	 * @return Mic\Interfaces\Response
	 */
	public function post(Request $request);
}