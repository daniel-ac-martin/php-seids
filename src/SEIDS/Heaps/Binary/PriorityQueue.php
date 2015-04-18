<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

use \SEIDS\Heaps\PriorityQueueHeapItem;

class PriorityQueue extends \SEIDS\Heaps\PriorityQueue
{
	public function __construct() // [\SplPriorityQueue]
	{
		$this->DataStructure = new PriorityQueueHeap(array($this, 'compare'));
	}
	
	public function extractInsert($value, $priority) // -> mixed
	{
		return $this->processExtract
		(
			$this->DataStructure->extractInsert
			(
				new PriorityQueueHeapItem($value, $priority)
			)
		);
	}
}

