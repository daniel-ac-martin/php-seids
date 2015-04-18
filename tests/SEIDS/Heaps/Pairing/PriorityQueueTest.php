<?php namespace SEIDS\Heaps\Pairing;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class PriorityQueueTest extends \SEIDS\Heaps\PriorityQueueTest
{
	protected function new_DS()
	{
		return new PriorityQueue;
	}
}

