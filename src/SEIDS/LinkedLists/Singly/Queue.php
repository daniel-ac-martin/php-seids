<?php namespace SEIDS\LinkedLists\Singly;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class Queue extends LinkedList
{
	////////////////////////////////////////////////////////////////////////////
	// Public methods - Implement the same interface as \SplQueue.
	////////////////////////////////////////////////////////////////////////////
	
	public function __construct() // [\SplQueue]
	{
		$this->flags = 4; // Note: I have no idea why SplQueue does this!
	}
	
	public function dequeue() // -> mixed [\SplQueue]
	{
		return $this->shift();
	}
	
	public function enqueue($value) // [\SplQueue]
	{
		// SplQueue documentation claims that this function returns void but
		// this does not seem to be the case!
		return $this->push($value);
	}
	
	//public function setIteratorMode($mode) // [\SplQueue]
}

