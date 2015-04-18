<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class MinHeap extends Heap
{
	protected function compare($value1, $value2) // -> int [\SplHeap]
	{
		return $value2 - $value1;
	}
}

