<?php

namespace Mic\Interfaces;

use Mic\Interfaces\Request;
use Mic\Interfaces\Response;

use Exception;

interface Plugin
{
	/**
	 * Returns an array of routes that this plugin should be called for
	 * @return Array
	 */
	public function getRoutes();
	
	/**
	 * Returns the  preferred order that this plugin should be invoked in 
	 * reference to all other plugins.
	 * @return Float
	 */
	public function getPreferredOrder();
	
	/**
	 * To be invoked before the routing process. (Before a request is 
	 * matched to the resource that will handle it)
	 * @param Request $request
	 */
	public function onStart(Request $request);
	
	/**
	 * To be invoked after the routing process when a resource has been found
	 * that can handle the request and before the resource method is called.
	 * @param Request $request
	 */
	public function preDispatch(Request $request);
	
	/**
	 * To be invoked after the resource method is called, with a Response
	 * instance as the result.
	 * @param Response $response
	 */
	public function postDispatch(Response $response);
	
	/**
	 * To be invoked after the response has been outputted.
	 * @param $response
	 */
	public function onEnd(Response $response);
	
	/**
	 * To be invoked when an exception occurs during processing. 
	 * @param Exception $exception
	 */
	public function onException(Exception $exception);
}