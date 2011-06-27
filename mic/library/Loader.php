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

namespace Mic\Library;

use Mic\Representations\Response\Template;

use InvalidArgumentException;

class Loader
{
	protected static $_baseDirectory;
	
	protected static $_baseDirectoryName;
	
	private static $_loadedFiles;
	
	public static function loadClass($className)
	{
		$namespace = explode("\\", $className);
		
		$rootNamespace = array_shift($namespace);
		$class = array_pop($namespace);
				
		//proceed for loading only if the base directory specified when this
		//loader was registered matches the root namespace
		if (strcasecmp($rootNamespace, static::$_baseDirectoryName) != 0) {
			//return;
		}
		
		$namespace = implode(DIRECTORY_SEPARATOR, $namespace);
		$subDirectory = strtolower($namespace);
		
		$filename = static::$_baseDirectory . DIRECTORY_SEPARATOR 
				  . $subDirectory . DIRECTORY_SEPARATOR 
				  . $class . '.php';
		
		include $filename;
 	}
	
	public static function loadTemplate($name)
	{
		//assumes that the directory where the templates are located is 
		//'template' and is under the base directory
		$filename = static::$_baseDirectory . DIRECTORY_SEPARATOR 
			  	  . 'templates' . DIRECTORY_SEPARATOR 
			  	  . $name . '.php';
			  
		$template = new Template($filename);
		return $template;
	}
	
	public static function registerAutoload($directory = null)
	{
		if (!is_dir($directory)) {
			throw new InvalidArgumentException("Invalid base directory");
		}
		
		static::$_baseDirectory = $directory;
		
		$path = explode(DIRECTORY_SEPARATOR, $directory);
		static::$_baseDirectoryName = strtolower(array_pop($path));
		
		spl_autoload_register(array(__CLASS__, 'loadClass'), true);
	}
}