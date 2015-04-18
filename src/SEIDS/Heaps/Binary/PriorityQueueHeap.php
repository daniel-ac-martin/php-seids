<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class PriorityQueueHeap extends Heap
{
	protected $callback_compare; // callable
	
	public function __construct(callable $callback_compare)
	{
		$this->callback_compare = $callback_compare;
		
		parent::__construct();
	}
	
	protected function compare($value1, $value2) // -> int [\SplHeap]
	{
		return call_user_func($this->callback_compare, $value1->priority, $value2->priority);
	}
	
	protected function value($value) // -> mixed [\SEIDS\Heaps\AbstractHeap]
	{
		return $value->value;
	}
}

