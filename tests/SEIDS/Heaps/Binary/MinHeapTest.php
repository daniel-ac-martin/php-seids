<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class MinHeapTest extends HeapTest
{
	protected function new_Heap($array = null)
	{
		return new MinHeap($array);
	}
	
	protected function new_SplHeap()
	{
		return new \SplMinHeap();
	}
}

