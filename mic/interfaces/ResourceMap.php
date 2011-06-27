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

interface ResourceMap
{
	/**
	 * Returns the resource that will handle a request uri.
	 * @param String $uri
	 * @return Mic\Interfaces\Resource
	 */
	public function getResource(Uri $uri);
	
	/**
	 * Returns true if there is a resource that can handle the request uri. 
	 * False otherwise.
	 * @param String $uri
	 * @return boolean
	 */
	public function resourceExists(Uri $uri);
	
	/**
	 * Add a resource to the map at a specified route.
	 * @param $route
	 * @param $resource
	 */
	public function addResource(Resource $resource, Route $route);
	
	/**
	 * Returns true if the specified route already exists.
	 * False otherwise
	 * @param String $route
	 * @return Boolean
	 */
	public function routeExists(Route $route);
}