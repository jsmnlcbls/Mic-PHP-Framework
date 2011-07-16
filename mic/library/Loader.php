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
	
	/**
	 * Holds the framework template directory
	 * @var String
	 */
	protected static $_frameworkTemplateDirectory;
	
	/**
	 * Holds the framework templates file extension that is used
	 * @var String
	 */
	protected static $_frameworkTemplateExtension;
	
	/**
	 * Holds the application template directory
	 * @var String
	 */
	protected static $_applicationTemplateDirectory;
	
	/**
	 * Holds the application template file extension that is used
	 * @var String
	 */
	protected static $_applicationTemplateExtension;
	
	/**
	 * Autoloader method for framework classes
	 * @param String $className Namespaced class name
	 * @return void 
	 */
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
	
	/**
	 * Autoloader method for application classes
	 * @param String $className Namespaced class name
	 * @return void
	 */
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
	
	/**
	 * Returns a framework template
	 * @param type $name the filename (without extension) of the framework template
	 * that should be loaded
	 * @return Template 
	 */
	public static function loadFrameworkTemplate($name)
	{
		$filename = static::$_frameworkTemplateDirectory . DIRECTORY_SEPARATOR 
			  	  . $name . "." . static::$_frameworkTemplateExtension;
		
		$template = new Template($filename);
		return $template;
	}
	
	/**
	 * Returns an application template
	 * @param String $name the filename (without extension) of the application
	 * template that should be loaded
	 * @return Template 
	 */
	public static function loadApplicationTemplate($name)
	{
		$filename = static::$_applicationTemplateDirectory . DIRECTORY_SEPARATOR
			  	  . $name . "." . static::$_applicationTemplateExtension;
			  
		$template = new Template($filename);
		return $template;
	}
	
	/**
	 * Registers this method to autoload framework classes with spl_autoload_register
	 * @param String $directory 
	 */
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
	
	/**
	 * Register this method to autoload application classes with spl_autoload_register
	 * @param String $directory 
	 */
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
	
	/**
	 * Set the framework template directory and file extension to be used.
	 * @param String $directory the template directory
	 * @param String $extension the file extension
	 */
	public static function configureFrameworkTemplateLoader($directory, $extension = "php")
	{
		if (!is_dir($directory)) {
			throw new InvalidArgumentException("Invalid template directory.");
		}
		
		if (!is_string($extension) || empty($extension)) {
			throw new InvalidArgumentException("Invalid file extension.");
		}
		
		static::$_frameworkTemplateDirectory = $directory;
		static::$_frameworkTemplateExtension = $extension;
	}
	
	/**
	 * Set the application template directory and file extension to be used.
	 * @param String $directory the template directory 
	 * @param String $extension the file extension
	 */
	public static function configureApplicationFrameworkLoader($directory, $extension = "php")
	{
		if (!is_dir($directory)) {
			throw new InvalidArgumentException("Invalid template directory.");
		}
		
		if (!is_string($extension) || empty($extension)) {
			throw new InvalidArgumentException("Invalid file extension.");
		}
		
		static::$_applicationTemplateDirectory = $directory;
		static::$_applicationTemplateExtension = $extension;
	}
	
	/**
	 * Returns the corresponding file path of namespaced class by treating the 
	 * namespace structure as a subdirectory under a root directory
	 * @param type $namespacedClass the namespaced class to load
	 * @param type $directory the root directory 
	 * @return string 
	 */
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
	
	/**
	 * Determines whether a root namespace matches a root directory
	 * @param type $namespace
	 * @param type $directory
	 * @return type 
	 */
	private static function _isNamespaceMatchingDirectory($namespace, $directory)
	{
		if (strcasecmp($namespace, $directory) != 0) {
			return false;
		}
		return true;
	}
}