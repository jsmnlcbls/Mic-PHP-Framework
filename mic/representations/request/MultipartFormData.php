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
use Mic\Representations\FileUpload;
use Mic\Interfaces\Filter;

class MultipartFormData implements Representation
{	
	protected $_data;
	
	protected $_size;
	
	public function __construct($data, $size)
	{
		$this->_size = $size;
		$this->_data = $data;
		//if array assume it is coming from $_POST and $_FILES
		if (is_array($data)) {
			$this->_processData();
		}
	}
	
	public function getMediaType()
	{
		return 'multipart/form-data';
	}
	
	public function getData()
	{
		return $this->_data;
	}
	
	public function getParsedData()
	{
		
	}
	
	public function getSize()
	{
		return $this->_size;
	}
		
	private function _processData()
	{
		foreach ($this->_data as $key => $value) {
			if ($this->_isFileData($value)) {
				$this->_data[$key] = $this->_getFileRepresentation($value);
			}
		}
	}
	
	/**
	 * Check if a data points to an uploaded file
	 * @param Array $data
	 * @return Boolean
	 */
	private function _isFileData(&$data)
	{
		if (!is_array($data)) {
			return false;
		}
		
		//if all the array keys present in a $_FILES array are present, this
		//is probably a file data
		if (isset($data['name']) && isset($data['type']) && 
			isset($data['size']) && isset($data['tmp_name']) && 
			isset($data['error'])) {

			return true;
		}
		return false;
	}
	
	private function _getFileRepresentation($fileData)
	{
		//if multiple files were uploaded with same name using the PHP [] convention
		if (is_array($fileData['name'])) {
			$fileCount = count($fileData['name']);
			$representation = array();
			foreach ($fileData['name'] as $index => $name) {
				$representation[$index] = new FileUpload($name, $index);
			}
			return $representation;
		} else {
			return new FileUpload($fileData['name']);
		}
	}
}