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
	/**
	 * Holds the name of the directory containing the framework. (not the full path)
	 * @var String 
	 */
	protected static $_frameworkDirectoryName;
	
	/**
	 * Holds the name of the directory containing the application(s). (not the full path)
	 * @var String
	 */
	protected static $_applicationDirectoryName;
	
	/**
	 * Holds the full path of the application directory.
	 * @var String
	 */
	protected static $_applicationRootDirectory;
	
	/**
	 * Holds the full path of the framework directory.
	 * @var String
	 */
	protected static $_frameworkRootDirectory;
	
	public static function loadFrameworkClass($className)
	{
		$namespaceParts = explode("\\", $className);
		$rootNamespace = array_shift($namespaceParts);
			
		if (!static::_isNamespaceMatchingDirectory($rootNamespace, 
					static::$_frameworkDirectoryName)) {
			return;
		}	
		
		$filename = static::_getClassFilePath($className, static::$_frameworkRootDirectory);
		include $filename;
 	}
	
	public static function loadApplicationClass($className)
	{
		$namespaceParts = explode("\\", $className);
		$rootNamespace = array_shift($namespaceParts);
			
		if (!static::_isNamespaceMatchingDirectory($rootNamespace, 
					static::$_applicationDirectoryName)) {
			return;
		}
		
		$filename = static::_getClassFilePath($className, static::$_applicationRootDirectory);
		include $filename;
	}
	
	public static function loadTemplate($name)
	{
		//assumes that the directory where the templates are located is 
		//'template' and is under the root framework directory
		$filename = static::$_frameworkRootDirectory . DIRECTORY_SEPARATOR 
			  	  . 'templates' . DIRECTORY_SEPARATOR 
			  	  . $name . '.php';
			  
		$template = new Template($filename);
		return $template;
	}
	
	public static function registerFrameworkLoader($directory = null)
	{
		if (!is_dir($directory)) {
			throw new InvalidArgumentException("Invalid base directory");
		}
		
		static::$_frameworkRootDirectory = $directory;
		
		$path = explode(DIRECTORY_SEPARATOR, $directory);
		static::$_frameworkDirectoryName = strtolower(array_pop($path));
		
		spl_autoload_register(array(__CLASS__, 'loadFrameworkClass'), true);
	}
	
	public static function registerApplicationLoader($directory = null)
	{
		if (!is_dir($directory)) {
			throw new InvalidArgumentException("Invalid base directory");
		}
		
		static::$_applicationRootDirectory = $directory;
		
		$path = explode(DIRECTORY_SEPARATOR, $directory);
		static::$_applicationDirectoryName = strtolower(array_pop($path));
		
		spl_autoload_register(array(__CLASS__, 'loadApplicationClass'), true);
	}
	
	private static function _getClassFilePath($namespacedClass, $directory)
	{
		$namespaceParts = explode("\\", $namespacedClass);
		$class = array_pop($namespaceParts);
		
		//remove the root namespace before constructing the subdirectory since
		//the directory also contains it.
		array_shift($namespaceParts);
		$subDirectory = strtolower(implode(DIRECTORY_SEPARATOR, $namespaceParts));
		
		$filename = $directory . DIRECTORY_SEPARATOR 
				  . $subDirectory . DIRECTORY_SEPARATOR 
				  . $class . '.php';
		
		return $filename;
	}
	
	private static function _isNamespaceMatchingDirectory($namespace, $directory)
	{
		if (strcasecmp($namespace, $directory) != 0) {
			return false;
		}
		return true;
	}
}