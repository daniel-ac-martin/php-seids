<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class MaxHeap extends Heap
{
	protected function compare($value1, $value2) // -> int [\SplHeap]
	{
		return $value1 - $value2;
	}
}

