<?php namespace SEIDS\Heaps\Pairing;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class MinPriorityQueue extends \SEIDS\Heaps\MinPriorityQueue
{
	public function __construct() // [\SplPriorityQueue]
	{
		$this->DataStructure = new PriorityQueueHeap(array($this, 'compare'));
	}
}

