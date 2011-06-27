<?php

namespace Mic\Library;

use Mic\Interfaces\FilterChain;
use Mic\Interfaces\Filter;

use UnexpectedValueException;

class DefaultFilterChain implements FilterChain
{
	protected $_chain;
	
	public function filter(&$data)
	{
		if (empty($this->_chain)) {
			throw new UnexpectedValueException("No filter found.");
		}
		
		foreach ($this->_chain as $filter) {
			$filter->filter($data);	
		}
	}
	
	public function chain(Filter $filter)
	{
		$this->_chain[] = $filter;
	}
}