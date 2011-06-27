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

interface Uri
{
	/**
	 * Returns the URI scheme. (HTTP usualy)
	 * @return String
	 */
	public function getScheme();
	
	/**
	 * Returns the host name of the server
	 * @return String
	 */
	public function getHost();
	
	/**
	 * Returns the port used on the server
	 * @return Int
	 */
	public function getPort();
	
	/**
	 * Returns the URI path. (Without the base path)
	 * @return String
	 */
	public function getPath();
	
	/**
	 * Returns the URI substring after the question mark "?" and before the
	 * "#" fragment mark if there is one. 
	 * @return String
	 */
	public function getQuery();
	
	/**
	 * Returns the base path if there is one set.
	 * @return String
	 */
	public function getBasePath();
	
	/**
	 * Returns the fragment part of the URI. (after the hashmark "#")
	 * @return String
	 */
	public function getFragment();
	
	/**
	 * Returns the URI path segments. Path segments are the series of characters
	 * separated by the slash ("/") character.
	 * @return Array
	 */
	public function getAllPathSegments();

	/**
	 * Get a specific path segment at offset starting from 0.
	 * @param Int $offset
	 * @return String
	 */
	public function getPathSegment($offset);
	
	/**
	 * Get a path segment immediately after another segment.
	 * @param String $segment the reference segment
	 * @return String
	 */
	public function getPathSegmentAfter($segment);
	
	/**
	 * Returns all query values in the URI
	 * @return Array
	 */
	public function getAllQueryValues();
	
	/**
	 * Get a specific query value
	 * @param String $key
	 */
	public function getQueryValue($key);
	
	/**
	 * Set the URI scheme
	 * @param String $scheme
	 */
	public function setScheme($scheme);
	
	/**
	 * Set the server host name
	 * @param String $host
	 */
	public function setHost($host);
	
	/**
	 * Set the server port that will be used
	 * @param Int $port
	 */
	public function setPort($port);
	
	/**
	 * Set the URI path
	 * @param String $path
	 */
	public function setPath($path);
	
	/**
	 * Set the URI query part
	 * @param String $query
	 */
	public function setQuery($query);
	
	/**
	 * Set the base path
	 * @param String $basePath
	 */
	public function setBasePath($basePath);
	
	/**
	 * Set the fragment part of the URI
	 * @param String $fragment
	 */
	public function setFragment($fragment);
	
	/**
	 * Returns a relative URI assembled using the URI parts
	 * @return String
	 */
	public function buildRelativeUri();
	
	/**
	 * Returns an absolute URI assembled using the URI parts
	 * @return String
	 */
	public function buildAbsoluteUri();
}