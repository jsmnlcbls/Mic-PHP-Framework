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

namespace Mic\Plugins\Cache;

use Mic\Interfaces\Request;
use Mic\Interfaces\Response;
use Mic\Interfaces\Plugin;
use Mic\Library\DefaultRequest;
use Mic\Library\DefaultResponse;
use Mic\Library\DefaultRoute;
use Mic\Library\System;

use Exception;

class EtagPlugin implements Plugin
{
	private $_requestEtag;
	
	public function getId()
	{
		return 'mic-etag';
	}
	
	public function getPreferredOrder()
	{
		return 11;
	}
	
	public function getRoutes()
	{
		return array(new DefaultRoute('/*', DefaultRoute::GET, '*'));
	}
	
	public function onStart(Request $request)
	{
		$header = DefaultRequest::HEADER_IF_NONE_MATCH;
		if ($request->headerExists($header)) {
			$this->_requestEtag = $request->getHeader($header);
		}
		//System::getInstance()->setResponse(new DefaultResponse(404));
	}
	
	public function preDispatch(Request $request) {}
	
	public function postDispatch(Response $response)
	{
		$responseEtag = md5($response->getRepresentation()->getData());
		
		$header = DefaultResponse::HEADER_ETAG;
		if ($this->_requestEtag === $responseEtag) {
			$response->setStatusCode(304);
		} elseif (!$response->headerExists($header)) {
			$response->setHeader($header, $responseEtag);
		}
	}
	
	public function onEnd(Response $response) {}
	
	public function onException(Exception $exception) {}
}