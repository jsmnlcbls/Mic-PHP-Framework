<?php 

use Mic\Library\Loader;
use Mic\Library\AbstractResource;
use Mic\Interfaces\Request;
use Mic\Library\DefaultResponse;

class Hello extends AbstractResource
{
	public function get(Request $request)
	{
		$response = new DefaultResponse(200);
		$representation = Loader::loadTemplate('hello');
		$representation->set('resourceFile', __FILE__);
		$representation->set('path', $request->getUri()->buildRelativeUri());
		$response->setRepresentation($representation);
		return $response;
	}
}