<?php

use \Mic\Library\AbstractResource;
use \Mic\Library\DefaultResponse;
use \Mic\Interfaces\Request;
use \Mic\Representations\TextPlain;

class __Wildcard extends AbstractResource
{
	public function get(Request $request)
	{
		$representation = new TextPlain("Hello");
		$response = new DefaultResponse();
		
		$response->setRepresentation($representation);
		return $response;
	}
}