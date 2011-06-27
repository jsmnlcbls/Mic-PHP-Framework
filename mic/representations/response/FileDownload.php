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

namespace Mic\Representations\Response;

use Mic\Interfaces\Representation;
use Mic\Interfaces\Filter;

class FileDownload implements Representation
{
	protected $_filename;
	
	protected $_mediaType;
	
	public function __construct($filename, $mediaType = null)
	{
		$this->_filename = (string) $filename;
		
		if (null !== $mediaType) {
			$this->_mediaType = $mediaType;
		}
	}
	
	public function getMediaType()
	{
		if (isset($this->_mediaType)) {
			return $this->_mediaType;
		}
		
		$type = new finfo(FILEINFO_MIME);
		return $type->file($this->_filename);
	}
	
	public function getSize()
	{
		return filesize($this->_filename);
	}
	
	public function getData()
	{
		return file_get_contents($this->_filename);
	}
	
	public function getFilename()
	{
		return $this->_filename;
	}
}