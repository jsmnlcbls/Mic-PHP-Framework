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

/**
 * Contains the settings available for the framework encapsulated in a class 
 * and accessible only using getter methods. The getter methods contains the 
 * default values for any setting. If you want to modify any setting, it is
 * preferred that you modify instead the corresponding class variable.
 * Example: If you want to modify the framework directory, set $_micDirectory to 
 * any valid directory of your choice instead of modifying the method 
 * getMicDirectory() to return the directory.
 */

namespace Mic;

class Settings
{
	/**
	 * Holds the framework root directory
	 * @var String
	 */
	protected $_micDirectory;
	
	/**
	 * Holds the application root directory
	 * @var String
	 */
	protected $_applicationDirectory;
	
	/**
	 * Holds the base path for all URI
	 * @var String
	 */
	protected $_basePath;
	
	/**
	 * Holds the file path of the System class
	 * @var String
	 */
	protected $_systemFilePath;
	
	/**
	 * Determines if an autoloader for the framework classes should be enabled
	 * @var Boolean
	 */
	protected $_enableFrameworkLoader;
	
	/**
	 * Determines if an autoloader for the application classes should be enabled
	 * @var Boolean
	 */
	protected $_enableApplicationLoader;
	
	/**
	 * Determines if a cache should be used that could potentially offer a boost
	 * in performance
	 * @var Boolean
	 */
	protected $_enableCache;
	
	/**
	 * Holds the directory for all resources
	 * @var String
	 */
	protected $_resourceDirectory;
	
	/**
	 * Holds what should be the particular filename of a resource file for it
	 * to be treated like the "*" wildcard operator when matching a URI to its
	 * corresponding resource.
	 * @var String
	 */
	protected $_resourceWildcardFilename;
	
	/**
	 * Holds what should be the name of a directory for it to be treated like 
	 * the "*" wildcard operator when matching a URI to its
	 * corresponding resource.
	 * @var String
	 */
	protected $_resourceWildcardDirectory;
	
	/**
	 * Holds the file extension for resource file. Defaults to php. All other 
	 * files that do not have this file extension will not be treated as a 
	 * resource.
	 * @var String
	 */
	protected $_resourceFileExtension;
		
	/**
	 * Holds the default content type to be placed in the response header.
	 * @var String
	 */
	protected $_defaultContentType;
	
	/**
	 * Determines whether or not the system should throw any exceptions encountered.
	 * Set this to true if you are debugging.
	 * @var Boolean
	 */
	protected $_throwException;
	
	/**
	 * Determines whether or not plugins should be invoked.
	 * @var Boolean
	 */
	protected $_enablePlugin;
	
	/**
	 * Holds the complete filepath and filename of the template to be shown when
	 * a resource is not found.
	 * @var String
	 */
	protected $_404ErrorTemplate;

	/**
	 * Holds the complete filepath and filename of the template to be shown when
	 * a resource does not allow certain methods.
	 * @var String
	 */
	protected $_405ErrorTemplate;
	
	/**
	 * Holds the complete filepath and filename of the template to be shown when
	 * there is an unexpected error/exception that occured.
	 * @var String
	 * @var String
	 */
	protected $_500ErrorTemplate;
	
	/**
	 * Returns the framework directory
	 * @return String
	 */
	public function getMicDirectory()
	{
		//if there is no directory set for the framework, assume that it is in
		//a subdirectory named 'mic' of the directory where this file is located
		if (!isset($this->_micDirectory)) {
			$this->_micDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'mic';
		}
		return $this->_micDirectory;
	}
	
	/**
	 * Returns the application directory
	 * @return String
	 */
	public function getApplicationDirectory()
	{
		//if there is no directory set for the application, defaults to a 
		//subdirectory named 'application' of the directory where this file is
		//located.
		if (!isset($this->_applicationDirectory)) {
			$this->_applicationDirectory = __DIR__ . DIRECTORY_SEPARATOR 
										 . 'application';
		}
		return $this->_applicationDirectory;
	}
	
	/**
	 * Returns the directory where all resources are located
	 * @return String
	 */
	public function getResourceDirectory()
	{
		//if there is no set resource directory, defaults to a 'resources'
		//subdirectory under the application directory.
		if (!isset($this->_resourceDirectory)) {
			$this->_resourceDirectory = $this->getApplicationDirectory() 
									  . DIRECTORY_SEPARATOR . 'resources';
		}
		return $this->_resourceDirectory;
	}
	
	/**
	 * Returns the base path for all URIs
	 * @return String
	 */
	public function getBasePath()
	{
		//if there is no base path set, attempt to guess it by checking the
		//document root and assuming that this file is also located under a
		//subdirectory of that document root.
		if (!isset($this->_basePath)) {
			$documentRoot = $_SERVER['DOCUMENT_ROOT'];
			$documentRoot = str_replace("/", DIRECTORY_SEPARATOR, $documentRoot);
			$basePath = str_replace($documentRoot, "", __DIR__);
			$this->_basePath = str_replace(DIRECTORY_SEPARATOR, "/", $basePath);
		}
		return $this->_basePath;
	}
	
	/**
	 * Returns the complete filepath for the System.php file
	 * @return String 
	 */
	public function getSystemFilePath()
	{
		//if there is no system file path set, defaults to the System.php file
		//in the library directory
		if (!isset($this->_systemFilePath)) {
			$this->_systemFilePath = $this->getMicDirectory() . DIRECTORY_SEPARATOR 
								   . 'library' . DIRECTORY_SEPARATOR . 'System.php';
		}
		return $this->_systemFilePath;
	}
	
	/**
	 * Returns true if it is desired that an autoloader for the framework classes
	 * should be registered with spl_autoload_register. False otherwise.
	 * @return Boolean
	 */
	public function isFrameworkLoaderEnabled()
	{
		if (!isset($this->_enableFrameworkLoader)) {
			$this->_enableFrameworkLoader = true;
		}
		return $this->_enableFrameworkLoader;
	}
	
	/**
	 * Returns true if is desired that an autoloader for the application classes
	 * should be registered with spl_autoload_register. False otherwise.
	 * @return Boolean
	 */
	public function isApplicationLoaderEnabled()
	{
		if (!isset($this->_enableApplicationLoader)) {
			$this->_enableApplicationLoader = true;
		}
		return $this->_enableApplicationLoader;
	}
	
	/**
	 * Returns true if it is desired that a caching system be used to boost
	 * performance. False otherwise.
	 * @return Boolean
	 */
	public function isCacheEnabled()
	{
		if (!isset($this->_enableCache)) {
			$this->_enableCache = false;
		}
		return $this->_enableCache;
	}
	
	/**
	 * Returns the particular name of a file that allows the resource class it 
	 * contains to be treated like the "*" wildcard operator when matching a 
	 * Request URI to a resource.
	 * @return String
	 */
	public function getResourceWildcardDirectory()
	{
		if (!isset($this->_resourceWildcardDirectory)) {
			$this->_resourceWildcardDirectory = '__Wildcard__';
		}
		return $this->_resourceWildcardDirectory;
	}
	
	/**
	 * Returns the particular name of a directory that allows it to be treated 
	 * like the "*" wildcard operator when matching a Request URI to a resource.
	 * @return String
	 */
	public function getResourceWildcardFilename()
	{
		if (!isset($this->_resourceWildcardFilename)) {
			$this->_resourceWildcardFilename = '__Wildcard__';
		}
		return $this->_resourceWildcardFilename;
	}
	
	/**
	 * Returns the file extension that is used by all resource files.
	 * @return String
	 */
	public function getResourceFileExtension()
	{
		if (!isset($this->_resourceFileExtension)) {
			$this->_resourceFileExtension = 'php';
		}
		return $this->_resourceFileExtension;
	}
	
	/**
	 * Returns the default content type to be used in an HTTP header for the 
	 * response.
	 * @return String
	 */
	public function getDefaultContentType()
	{
		if (!isset($this->_defaultContentType)) {
			$this->_defaultContentType = 'text/html';
		}
		return $this->_defaultContentType;
	}
	
	/**
	 * Returns true if any exceptions encountered are set to be thrown by the 
	 * system. False otherwise.
	 * @return Boolean
	 */
	public function isExceptionThrown()
	{
		if (!isset($this->_throwException)) {
			$this->_throwException = false;
		}
		return $this->_throwException;
	}
	
	/**
	 * Returns true if plugins are to be invoked by the system during processing.
	 * False otherwise.
	 * @return Boolean
	 */
	public function isPluginEnabled()
	{
		if (!isset($this->_enablePlugin)) {
			$this->_enablePlugin = false;
		}
		return $this->_enablePlugin;
	}
	
	/**
	 * Returns the template to be used when a resource for a particular URI is
	 * not found.
	 * @return String
	 */
	public function get404ErrorTemplate()
	{
		if (!isset($this->_404ErrorTemplate)) {
			$this->_404ErrorTemplate = $this->getMicDirectory() 
									 . DIRECTORY_SEPARATOR . 'templates'
									 . DIRECTORY_SEPARATOR . '404.php';
		}
		return $this->_404ErrorTemplate;
	}
	
	/**
	 * Returns the template file to be displayed when a resource does not support
	 * a particular HTTP method.
	 * @return String
	 */
	public function get405ErrorTemplate()
	{
		if (!isset($this->_405ErrorTemplate)) {
			$this->_405ErrorTemplate = $this->getMicDirectory() 
									 . DIRECTORY_SEPARATOR . 'templates'
									 . DIRECTORY_SEPARATOR . '405.php';
		}
		return $this->_405ErrorTemplate;
	}
	
	/**
	 * Returns the template to be used for unexpected errors that occurs during
	 * processing.
	 * @return String
	 */
	public function get500ErrorTemplate()
	{
		if (!isset($this->_500ErrorTemplate)) {
			$this->_500ErrorTemplate = $this->getMicDirectory() 
									 . DIRECTORY_SEPARATOR . 'templates'
									 . DIRECTORY_SEPARATOR . '500.php';
		}
		return $this->_500ErrorTemplate;
	}
}