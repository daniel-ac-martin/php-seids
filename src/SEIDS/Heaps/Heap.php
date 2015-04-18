<?php namespace SEIDS\Heaps;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

abstract class Heap implements \Countable, \Iterator
{
	////////////////////////////////////////////////////////////////////////////
	// Abstract methods
	////////////////////////////////////////////////////////////////////////////
	
	abstract public    function __construct($array = null); //          [\SplHeap]
	abstract protected function compare($value1, $value2);  // -> int   [\SplHeap]
	abstract public    function extract();                  // -> mixed [\SplHeap]
	abstract public    function insert($value);             //          [\SplHeap]
	abstract public    function recoverFromCorruption();    //          [\SplHeap]
	abstract public    function top();                      // -> mixed [\SplHeap]
	abstract public    function update($value1, $value2);
	
	////////////////////////////////////////////////////////////////////////////
	// Members
	////////////////////////////////////////////////////////////////////////////
	
	protected $size = 0; // int
	
	////////////////////////////////////////////////////////////////////////////
	// Public methods - Implement the same interface as \SplHeap but with the
	//                  ability to update.
	////////////////////////////////////////////////////////////////////////////
	
	public function count() // -> int [\Countable]
	{
		return $this->size;
	}
	
	public function current() // -> mixed [\Iterator]
	{
		$r = null;
		
		if($this->valid())
		{
			$r = $this->top();
		}
		
		return $r;
	}
	
	public function isEmpty() // -> bool [\SplHeap]
	{
		return !$this->valid();
	}
	
	public function key() // -> mixed [\Iterator]
	{
		// I don't understand the logic behind this but this is what SplHeap
		// seems to do.
		return $this->size - 1;
	}
	
	public function next() // [\Iterator]
	{
		if($this->valid())
		{
			$this->extract();
		}
	}
	
	public function rewind() // [\Iterator]
	{
		// No-op
	}
	
	public function valid() // -> bool [\Iterator]
	{
		return $this->size > 0;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Protected methods
	////////////////////////////////////////////////////////////////////////////
	
	// Overload this function in order to separate data/values from their
	// position/priority in the heap.
	protected function value($value) // -> mixed
	{
		return $value;
	}
	
	protected function hashtIndex($value) // -> mixed
	{
		$v = $this->value($value);
		$r = null;
		
		if($v instanceof \SplInt)
		{
			$r = (int)$v;
		}
		else if($v instanceof \SplFloat)
		{
			$r = (float)$v;
		}
		else if($v instanceof \SplBool)
		{
			$r = (bool)$v;
		}
		else if($v instanceof \SplString)
		{
			$r = (string)$v;
		}
		else if(is_object($v))
		{
			$r = spl_object_hash($v);
		}
		else
		{
			$r = $v;
		}
		
		return $r;
	}
}

