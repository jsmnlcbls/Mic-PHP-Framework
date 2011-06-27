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

use Mic\Responses\Download;

use \Mic\Interfaces\Response;
use \Mic\Interfaces\Representation;
use \InvalidArgumentException;
use \UnexpectedValueException;
use \OutOfRangeException;

class DefaultResponse implements Response
{
	const
	HEADER_ACCEPT_RANGES = 'Accept-Ranges',
	HEADER_AGE = 'Age',
	HEADER_ALLOW = 'Allow',
	HEADER_CACHE_CONTROL = 'Cache-Control',
	HEADER_CONTENT_ENCODING = 'Content-Encoding',
	HEADER_CONTENT_LANGUAGE = 'Content-Language',
	HEADER_CONTENT_LENGTH = 'Content-Length',
	HEADER_CONTENT_LOCATION = 'Content-Location',
	HEADER_CONTENT_DISPOSITION = 'Content-Disposition',
	HEADER_CONTENT_MD5 = 'Content-MD5',
	HEADER_CONTENT_RANGE = 'Content-Range',
	HEADER_CONTENT_TYPE = 'Content-Type',
	HEADER_DATE = 'Date',
	HEADER_ETAG = 'ETag',
	HEADER_EXPIRES = 'Expires',
	HEADER_LAST_MODIFIED = 'Last-Modified',
	HEADER_LOCATION = 'Location',
	HEADER_PRAGMA = 'Pragma',
	HEADER_PROXY_AUTHENTICATE = 'Proxy-Authenticate',
	HEADER_RETRY_AFTER = 'Retry-After',
	HEADER_SERVER = 'Server',
	HEADER_SET_COOKIE = 'Set-Cookie',
	HEADER_TRAILER = 'Trailer',
	HEADER_TRANSFER_ENCODING = 'Transfer-Encoding',
	HEADER_VARY = 'Vary',
	HEADER_VIA = 'Via',
	HEADER_WARNING = 'Warning',
	HEADER_WWW_AUTHENTICATE = 'WWW-Authenticate';
	
	const
	STATUS_CONTINUE = 100,
	STATUS_SWITCHING_PROTOCOLS = 101,
	STATUS_OK = 200,
	STATUS_CREATED = 201,
	STATUS_ACCEPTED = 202,
	STATUS_NON_AUTHORITATIVE_INFORMATION = 203,
	STATUS_NO_CONTENT = 204,
	STATUS_RESET_CONTENT = 205,
	STATUS_PARTIAL_CONTENT = 206,
	STATUS_MULTIPLE_CHOICES = 300,
	STATUS_MOVED_PERMANENTLY = 301,
	STATUS_FOUND = 302,
	STATUS_SEE_OTHER = 303,
	STATUS_NOT_MODIFIED = 304,
	STATUS_USE_PROXY = 305,
	STATUS_TEMPORARY_REDIRECT = 307,
	//Client Error
	STATUS_BAD_REQUEST = 400,
	STATUS_UNAUTHORIZED = 401,
	STATUS_FORBIDDEN = 403,
	STATUS_NOT_FOUND = 404,
	STATUS_METHOD_NOT_ALLOWED = 405,
	STATUS_NOT_ACCEPTABLE = 406,
	STATUS_PROXY_AUTHENTICATION_REQUIRED = 407,
	STATUS_REQUEST_TIMEOUT = 408,
	STATUS_CONFLICT = 409,
	STATUS_GONE = 410,
	STATUS_LENGTH_REQUIRED = 411,
	STATUS_PRECONDITION_FAILED = 412,
	STATUS_REQUEST_ENTITY_TOO_LARGE = 413,
	STATUS_REQUEST_URI_TOO_LONG = 414,
	STATUS_UNSUPPORTED_MEDIA_TYPE = 415,
	STATUS_EXPECTATION_FAILED = 417,
	//Server Error
	STATUS_INTERNAL_SERVER_ERROR = 500,
	STATUS_NOT_IMPLEMENTED = 501,
	STATUS_BAD_GATEWAY = 502,
	STATUS_SERVICE_UNAVAILABLE = 503,
	STATUS_GATEWAY_TIMEOUT = 504,
	STATUS_HTTP_VERSION_NOT_SUPPORTED = 505;
	
	protected $_header;
	
	protected $_representation;
	
	protected $_statusCode;
	
	protected $_variables;
	
	public function __construct($statusCode = 200)
	{
		$this->setStatusCode($statusCode);
	}
	
	public function getHeader($name)
	{
		if (empty($name) || !is_string($name)) {
			throw new InvalidArgumentException("Invalid header name");
		}
		
		if (isset($this->_header[$name])) {
			return $this->_header[$name];
		}
		
		throw new UnexpectedValueException("No matching header found");
	}
	
	public function getRepresentation()
	{
		if (!isset($this->_representation)) {
			return new EmptyRepresentation();
		}
		
		return $this->_representation;
	}
	
	public function getStatusCode()
	{
		return $this->_statusCode;
	}
	
	public function headerExists($name)
	{
		if (empty($name) || !is_string($name)) {
			throw new InvalidArgumentException("Invalid header name");
		}
		
		if (isset($this->_header[$name])) {
			return true;
		}
		return false;
	}
	
	public function setHeader($name, $value)
	{
		if (!is_string($name) || empty($name)) {
			throw new InvalidArgumentException("Invalid header name");
		}
		
		if (!is_string($value)) {
			throw new InvalidArgumentException("Header value should be string.");
		}
		
		$this->_header[$name] = $value;
	}
	
	public function setRepresentation(Representation $representation)
	{
		$this->_representation = $representation;
	}
	
	public function setStatusCode($statusCode)
	{
		if ($statusCode < 100 || $statusCode > 599) {
			throw new OutOfRangeException("Status code is out of range.");
		}
		
		if (false !== strpos("$statusCode", ".")) {
			throw new InvalidArgumentException("Invalid status code.");
		}
		$this->_statusCode = (int) $statusCode;
	}
	
	public function output()
	{
		$mediaType = '';
		$size = null;
		if (isset($this->_representation)) {
			$mediaType = (string) $this->_representation->getMediaType();
			$size = $this->_representation->getSize();
		}
		if (!empty($mediaType)) {
			$this->setHeader(self::HEADER_CONTENT_TYPE, $mediaType);
		}
		
		if (null !== $size) {
			$this->setHeader(self::HEADER_CONTENT_LENGTH, (string) $size);
		}
		
		if ($this->_shouldEntityBeOutputted()) {
			header('HTTP/1.1 ' . $this->_statusCode);
			if (count($this->_header) > 0) {
				$this->_outputHeaders();
			}
			if ($this->_shouldRepresentationBeOutputted()) {
				echo $this->getRepresentation()->getData();
			}
		}
	}
	
	protected function _outputHeaders()
	{
		
		foreach ($this->_header as $name => $value) {
			$header = trim($name) . ': ' . $value;
			header($header);
		}
	}
	
	protected function _shouldEntityBeOutputted()
	{
		if ($this->_statusCode == self::STATUS_RESET_CONTENT) {
			return false;
		}
		return true;
	}
	
	protected function _shouldRepresentationBeOutputted()
	{
		if ($this->_statusCode == self::STATUS_NOT_MODIFIED || 
			$this->_statusCode == self::STATUS_NO_CONTENT) {
			return false;		
		}
		return true;
	}
}
