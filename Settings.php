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


namespace Mic;

class Settings
{
	protected $_micDirectory;
	
	protected $_applicationDirectory;
	
	protected $_basePath;
	
	protected $_systemFilePath;
	
	protected $_enableAutoloader;
	
	protected $_enableCache;
	
	protected $_resourceDirectory;
	
	protected $_resourceWildcardFilename;
	
	protected $_resourceWildcardDirectory;
	
	protected $_resourceFileExtension;
		
	protected $_defaultContentType;
	
	protected $_throwException;
	
	protected $_enablePlugin;
	
	protected $_404ErrorTemplate;
	
	protected $_405ErrorTemplate;
	
	protected $_500ErrorTemplate;
	
	public function getMicDirectory()
	{
		//if there is no directory set for the framework, assume that it is in
		//a subdirectory named 'mic' of the directory where this file is located
		if (!isset($this->_micDirectory)) {
			$this->_micDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'mic';
		}
		return $this->_micDirectory;
	}
	
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
	
	
	public function isAutoloaderEnabled()
	{
		if (!isset($this->_enableAutoloader)) {
			$this->_enableAutoloader = true;
		}
		return $this->_enableAutoloader;
	}
	
	public function isCacheEnabled()
	{
		if (!isset($this->_enableCache)) {
			$this->_enableCache = false;
		}
		return $this->_enableCache;
	}
	
	public function getResourceWildcardDirectory()
	{
		if (!isset($this->_resourceWildcardDirectory)) {
			$this->_resourceWildcardDirectory = '__Wildcard__';
		}
		return $this->_resourceWildcardDirectory;
	}
	
	public function getResourceWildcardFilename()
	{
		if (!isset($this->_resourceWildcardFilename)) {
			$this->_resourceWildcardFilename = '__Wildcard__';
		}
		return $this->_resourceWildcardFilename;
	}
	
	public function getResourceFileExtension()
	{
		if (!isset($this->_resourceFileExtension)) {
			$this->_resourceFileExtension = 'php';
		}
		return $this->_resourceFileExtension;
	}
	
	public function getDefaultContentType()
	{
		if (!isset($this->_defaultContentType)) {
			$this->_defaultContentType = 'text/html';
		}
		return $this->_defaultContentType;
	}
	
	public function isExceptionThrown()
	{
		if (!isset($this->_throwException)) {
			$this->_throwException = false;
		}
		return $this->_throwException;
	}
	
	public function isPluginEnabled()
	{
		if (!isset($this->_enablePlugin)) {
			$this->_enablePlugin = false;
		}
		return $this->_enablePlugin;
	}
	
	public function get404ErrorTemplate()
	{
		if (!isset($this->_404ErrorTemplate)) {
			$this->_404ErrorTemplate = $this->getMicDirectory() 
									 . DIRECTORY_SEPARATOR . 'templates'
									 . DIRECTORY_SEPARATOR . '404.php';
		}
		return $this->_404ErrorTemplate;
	}
	
	public function get405ErrorTemplate()
	{
		if (!isset($this->_405ErrorTemplate)) {
			$this->_405ErrorTemplate = $this->getMicDirectory() 
									 . DIRECTORY_SEPARATOR . 'templates'
									 . DIRECTORY_SEPARATOR . '405.php';
		}
		return $this->_405ErrorTemplate;
	}
	
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