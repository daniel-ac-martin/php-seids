<?php namespace SEIDS\Heaps;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class PriorityQueueHeapItem
{
	public $value    = null;
	public $priority = null;
	
	public function __construct($value, $priority)
	{
		$this->value    = $value;
		$this->priority = $priority;
	}
}

