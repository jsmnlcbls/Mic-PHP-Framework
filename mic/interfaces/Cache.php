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

interface Cache
{
	/**
	 * Returns a value stored in the cache
	 * @param String $key
	 * @return Mixed
	 */
	public function get($key);
	
	/**
	 * Store a value identified by key in the cache
	 * @param String $key
	 * @param Mixed $value
	 */
	public function store($key, $value);
	
	/**
	 * Check if a key is already in use
	 * @param String $key
	 * @return Boolean
	 */
	public function exists($key);
	
	/**
	 * Clear a previously stored value in the cache
	 * @param String $key
	 */
	public function clear($key);
	
	/**
	 * Empty the cache
	 */
	public function clearAll();
}