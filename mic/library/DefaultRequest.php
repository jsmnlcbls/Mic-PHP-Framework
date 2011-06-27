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

use Mic\Representations\EmptyRepresentation;
use Mic\Representations\RawData;
use Mic\Representations\Request\FormUrlEncoded;
use Mic\Representations\Request\MultipartFormData;

use Mic\Interfaces\Request;
use Mic\Interfaces\Uri;
use Mic\Interfaces\Representation;
use Mic\Representations\FileUpload;

use InvalidArgumentException;
use UnexpectedValueException;


class DefaultRequest implements Request
{
	//Header Constants
	const 	
	HEADER_ACCEPT = 'ACCEPT',
	HEADER_ACCEPT_CHARSET = 'ACCEPT-CHARSET',
	HEADER_ACCEPT_ENCODING = 'ACCEPT-ENCODING',
	HEADER_ACCEPT_LANGUAGE = 'ACCEPT-LANGUAGE',
	HEADER_ACCEPT_RANGES = 'Accept-Ranges',
	HEADER_AUTHORIZATION = 'Authorization',
	HEADER_CACHE_CONTROL = 'Cache-Control',
	HEADER_CONNECTION = 'CONNECTION',
	HEADER_COOKIE = 'Cookie',
	HEADER_CONTENT_LENGTH = 'Content-Length',
	HEADER_CONTENT_TYPE = 'Content-Type',
	HEADER_DATE = 'Date',
	HEADER_EXPECT = 'Expect',
	HEADER_FROM = 'From',
	HEADER_HOST = 'HOST',
	HEADER_IF_MATCH = 'If-Match',
	HEADER_IF_MODIFIED_SINCE = 'If-Modified-Since',
	HEADER_IF_NONE_MATCH = 'If-None-Match',
	HEADER_IF_RANGE = 'If-Range',
	HEADER_IF_UNMODIFIED_SINCE = 'If-Unmodified-Since',
	HEADER_MAX_FORWARDS = 'Max-Forwards',
	HEADER_PRAGMA = 'Pragma',
	HEADER_PROXY_AUTHORIZATION = 'Proxy-Authorization',
	HEADER_RANGE = 'Range',
	HEADER_REFERER = 'Referer',
	HEADER_TE = 'TE',
	HEADER_UPGRADE = 'Upgrade',
	HEADER_USER_AGENT = 'User-Agent',
	HEADER_VIA = 'Via',
	HEADER_WARNING = 'Warning';
	
	//Supported request methods
	const 
	METHOD_GET = 'GET',
	METHOD_POST = 'POST',
	METHOD_PUT = 'PUT',
	METHOD_DELETE = 'DELETE';
	
	//Supported media types that will be processed
	const 
	MEDIA_TYPE_FORM_URL_ENCODED = 'application/x-www-form-urlencoded',
	MEDIA_TYPE_MULTIPART_FORM_DATA = 'multipart/form-data';
	
	protected $_uri;
	
	protected $_method;
	
	protected $_header;
	
	protected $_representation;
	
	protected $_variables;
	
	public function setUri(Uri $uri)
	{
		$this->_uri = $uri;
	}
	
	public function setMethod($method)
	{
		if (!is_string($method) || empty($method)) {
			throw new InvalidArgumentException("Invalid request method.");
		}
		$this->_method = strtoupper($method);
	}
	
	public function setHeader($name, $value)
	{
		if (!is_string($name) || empty($name)) {
			throw new InvalidArgumentException("Invalid header name");
		}
		
		if (!is_string($value)) {
			throw new InvalidArgumentException("Invalid header value");
		}
		
		$this->_header[$name] = $value;
	}
	
	public function setRepresentation(Representation $representation)
	{
		$this->_representation = $representation;
	}
	
	public function getUri()
	{
		if (!isset($this->_uri)) {
			$this->_uri = new DefaultUri;
		}
		return $this->_uri;
	}
	
	public function getMethod()
	{
		if (!isset($this->_method)) {
			$this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
		}
		
		if (empty($this->_method)) {
			throw new UnexpectedValueException("No request method found");
		}
		
		return $this->_method;
	}
	
	public function getHeader($name)
	{
		if (!is_string($name) || empty($name)) {
			throw new InvalidArgumentException("Invalid header name");
		}
		
		$header = $this->_getHeader($name);
		if (null !== $header) {
			return $header;
		}
		
		throw new UnexpectedValueException("No value found for header $name");
	}
	
	public function getRepresentation()
	{		
		$method = $this->getMethod();
		if ($method == self::METHOD_GET || $method == self::METHOD_DELETE) {
			return new EmptyRepresentation();
		}
		
		if (isset($this->_representation)) {
			return $this->_representation;
		}
		
		$contentType = $this->_getContentType();
		$contentLength = $this->_getContentLength();
		if ($method == self::METHOD_POST && 
			$contentType == self::MEDIA_TYPE_FORM_URL_ENCODED) {
				
			return new FormUrlEncoded($_POST, $contentLength);
		} elseif ($method == self::METHOD_POST && 
				  $contentType == self::MEDIA_TYPE_MULTIPART_FORM_DATA) {
				  	
			$data = array_merge($_POST, $_FILES);
			return new MultipartFormData($data, $contentLength);
		} elseif ($method == self::METHOD_PUT && 
				  $contentType == self::MEDIA_TYPE_FORM_URL_ENCODED) {
			
			return new FormUrlEncoded($_POST, $contentLength);
		} else {
			$data = file_get_contents("php://input");
			return new RawData($contentType, $data, $contentLength);
		}
	}
	
	public function headerExists($name)
	{
		$name = (string) $name;
		
		if (null !== $this->_getHeader($name)) {
			return true;
		}
		return false;
	}
	
	protected function _getContentType()
	{
		if ($this->headerExists('Content-Type')) {
			return strtolower($this->getHeader('Content-Type'));
		}
		if (isset($_SERVER['CONTENT_TYPE'])) {
			return strtolower($_SERVER['CONTENT_TYPE']);
		}
		throw new UnexpectedValueException("No content type was found.");
	}
	
	protected function _getContentLength()
	{
		if ($this->headerExists('Content-Length')) {
			return $this->getHeader('Content-Length');
		}
		if (isset($_SERVER['CONTENT_LENGTH'])) {
			return $_SERVER['CONTENT_LENGTH'];
		}
		throw new UnexpectedValueException("No content length was found.");
	}
	
	protected function _getHeader($name)
	{
		if (isset($this->_header[$name])) {
			return $this->_header[$name];
		} else {
			$header = $this->_getHeaderIgnoreCase($name);
			if (null !== $header) {
				return $header;
			}
			
			$header = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
	        if (!empty($_SERVER[$header])) {
	        	return $_SERVER[$header];     
	        }
		}
		
		return null;
	}
	
	protected function _getUri()
	{
		//From Drupal
		if (isset($_SERVER['REQUEST_URI'])) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			if (isset($_SERVER['argv'])) {
				$uri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['argv'][0];
			} elseif (isset($_SERVER['QUERY_STRING'])) {
		    	$uri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
		    } else {
		      $uri = $_SERVER['SCRIPT_NAME'];
		    }
		}
		$uri = '/'. ltrim($uri, '/');
		
		return $uri;
	}
	
	protected function _getHeaderIgnoreCase($name)
	{
		if (empty($this->_header)) {
			return null;	
		}
		$headers = array_keys($this->_header);
		foreach ($headers as $headerName) {
			if (0 === strcasecmp($name, $headerName)) {
				return $this->_header[$headerName];
			}
		}
		return null;
	}
}