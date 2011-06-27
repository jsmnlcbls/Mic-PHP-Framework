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

namespace Mic\Responses;

use Mic\Representations\Response\Template;
use Mic\Library\DefaultResponse;

class Html405 extends DefaultResponse {

	public function __construct($method, $allowedMethod, $template) 
	{
		$template = new Template($template);
		$allowedList = implode(', ', $allowedMethod);
		$template->set('allowedMethod', $allowedList);
		$template->set('method', $method);
		$this->setRepresentation($template);
		
		$this->setHeader(DefaultResponse::HEADER_ALLOW, $allowedList);
		parent::__construct(405);
	}

}