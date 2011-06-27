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

use Mic\Representations\Template;
use Mic\Interfaces\ExceptionHandler;
use Mic\Interfaces\Request;
use Mic\Responses\Html404;
use Mic\Responses\Html405;
use Mic\Responses\Html500;
use Mic\Exceptions\MethodNotAllowedException;
use Mic\Exceptions\ResourceNotFoundException;

use Exception;

class DefaultExceptionHandler implements ExceptionHandler
{
	protected $_404Template;
	
	protected $_405Template;
	
	protected $_500Template;
	
	public function handle(Exception $exception, Request $request)
	{
		$accept = '';
		if ($request->headerExists(DefaultRequest::HEADER_ACCEPT)) {
			$accept = $request->getHeader(DefaultRequest::HEADER_ACCEPT);
		}
		//if the client accepts a text/html response
		
		if (false !== strstr($accept, 'text/html')) {
			if ($exception instanceof ResourceNotFoundException) {
				$uri = $exception->getUri();
				return new Html404($uri, $this->_404Template);
			} elseif ($exception instanceof MethodNotAllowedException) {
				$method = $exception->getMethod();
				$allowedMethod = $exception->getAllowedMethod();
				$response = new Html405($method, $allowedMethod, $this->_405Template);
				return $response;
			} else {
				$template = $this->_500Template;
				return new Html500($template);
			}
		} else {
			if ($exception instanceof ResourceNotFoundException) {
				return new DefaultResponse(404);
			} elseif ($exception instanceof MethodNotAllowedException) {
				$response = new DefaultResponse(405);
				$allowed = $exception->getAllowedMethod();
				$allowedList = implode(', ', $allowed);
				$response->setHeader('ALLOWED', $allowedList);
				return $response;
			} else {
				return new DefaultResponse(500);
			}
		}
	}
	
	public function set404Template($filename)
	{
		$this->_404Template = $filename;
	}
	
	public function set405Template($filename)
	{
		$this->_405Template = $filename;
	}
	
	public function set500Template($filename)
	{
		$this->_500Template = $filename;
	}
}