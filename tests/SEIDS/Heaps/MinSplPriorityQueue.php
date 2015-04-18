<?php namespace SEIDS\Heaps;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class MinSplPriorityQueue extends \SplPriorityQueue
{
	public function compare($priority1, $priority2)
	{
		return $priority2 - $priority1;
	}
}

