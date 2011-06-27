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

namespace Mic\Representations\Request;

use Mic\Interfaces\Representation;
use Mic\Interfaces\Filter;

class FileUpload implements Representation
{
	const NO_ERROR = UPLOAD_ERR_OK;
	
	const EXCEEDS_PHP_MAX_FILESIZE_ERROR = UPLOAD_ERR_INI_SIZE;
	
	const EXCEEDS_FORM_MAX_FILESIZE_ERROR = UPLOAD_ERR_FORM_SIZE;
	
	const PARTIAL_UPLOAD_ERROR = UPLOAD_ERR_PARTIAL;
	
	const NO_UPLOADED_FILE_ERROR = UPLOAD_ERR_NO_FILE;
	
	const NO_TEMP_DIRECTORY_ERROR = UPLOAD_ERR_NO_TMP_DIR;
	
	const DISK_WRITE_ERROR = UPLOAD_ERR_CANT_WRITE;
	
	const PHP_EXTENSION_ERROR = UPLOAD_ERR_EXTENSION;
	
	protected $_mediaType;
	
	protected $_size;
	
	protected $_tmpFile;
	
	protected $_filename;
	
	protected $_errorCode;
	
	public function __construct($name, $index = null)
	{
		$fileData = $_FILES[$name];
		
		if (null !== $index) {
			$this->_mediaType 	= $fileData['type'][$index];
			$this->_size 		= $fileData['size'][$index];
			$this->_tmpFile 	= $fileData['tmp_name'][$index];
			$this->_filename 	= $fileData['name'][$index];
			$this->_errorCode	= $fileData['error'][$index];
		} else {
			$this->_mediaType 	= $fileData['type'];
			$this->_size 		= $fileData['size'];
			$this->_tmpFile 	= $fileData['tmp_name'];
			$this->_filename 	= $fileData['name'];
			$this->_errorCode	= $fileData['error'];
		}
	}
	
	public function getMediaType()
	{
		return $this->_mediaType;
	}
	
	public function getSize()
	{
		return $this->_size;
	}
	
	public function getData()
	{	
		return file_get_contents($this->_tmpFile);
	}
		
	/**
	 * Returns the original filename of the uploaded file on the client machine
	 * @return String
	 */
	public function getFilename()
	{
		return $this->_filename;
	}
	
	/**
	 * Move the file into another directory using the specified filename
	 * @param String $directory
	 * @param String $filename
	 * @throws UnexpectedValueException if there was an error moving the file
	 */
	public function moveFile($directory, $filename)
	{
		$destination = $directory . DIRECTORY_SEPARATOR . $filename;
		$result = move_uploaded_file($this->_tmpFile, $destination);
		if (false === $result) {
			throw new UnexpectedValueException("Unable to move uploaded file.");
		}
	}
	
	/**
	 * Returns true if the file was uploaded successfully. False otherwise.
	 * @return Boolean
	 */
	public function isUploadSuccessful()
	{
		if (self::NO_ERROR == $this->_errorCode) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the error code if the file was not uploaded successfully. 
	 */
	public function getErrorCode()
	{
		if (self::NO_ERROR != $this->_errorCode) {
			return $this->_errorCode;
		}
		return self::NO_ERROR;
	}
}