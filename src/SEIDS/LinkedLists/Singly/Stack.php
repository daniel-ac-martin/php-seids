<?php namespace SEIDS\LinkedLists\Singly;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

use \SEIDS\LinkedLists\CantPopFromEmptyException;

class Stack implements \Countable
{
	////////////////////////////////////////////////////////////////////////////
	// Members
	////////////////////////////////////////////////////////////////////////////
	
	protected $ds; // &LinkedList
	
	////////////////////////////////////////////////////////////////////////////
	// Public methods - Implement a tiny subset of \SplStack. We cannot
	//                  implement the full interface as we do not support LIFO.
	////////////////////////////////////////////////////////////////////////////
	
	public function __construct() // [\SplQueue]
	{
		$this->ds = new LinkedList;
	}
	
	public function bottom() // -> mixed [\SplQueue]
	{
		return $this->ds->top();
	}
	
	public function count() // -> int [\Countable]
	{
		return $this->ds->count();
	}
	
	public function isEmpty() // -> bool [\SplQueue]
	{
		return $this->ds->isEmpty();
	}
	
	public function pop() // -> mixed [\SplQueue]
	{
		$r = null;
		
		if($this->isEmpty())
		{
			throw new CantPopFromEmptyException('Can\'t pop from an empty datastructure');
		}
		else
		{
			$r = $this->ds->shift();
		}
		
		return $r;
	}
	
	public function push($value) // [\SplQueue]
	{
		// SplStack documentation claims that this function returns void but
		// this does not seem to be the case!
		return $this->ds->unshift($value);
	}
	
	//public function setIteratorMode($mode) // [\SplQueue]
	
	public function top() // -> mixed [\SplQueue]
	{
		return $this->ds->bottom();
	}
}

