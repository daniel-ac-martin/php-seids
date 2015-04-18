<?php namespace SEIDS\Heaps;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

abstract class PriorityQueue implements \Countable, \Iterator
{
	////////////////////////////////////////////////////////////////////////////
	// Abstract methods
	////////////////////////////////////////////////////////////////////////////
	
	abstract public function __construct(); // [\SplPriorityQueue]
	
	////////////////////////////////////////////////////////////////////////////
	// Members
	////////////////////////////////////////////////////////////////////////////
	
	const EXTR_DATA     = 1;
	const EXTR_PRIORITY = 2;
	const EXTR_BOTH     = 3;
	
	protected $DataStructure;
	protected $flags         = 1; // int
	
	////////////////////////////////////////////////////////////////////////////
	// Public methods - Implement the same interface as \SplPriorityQueue but
	//                  with the ability to update.
	////////////////////////////////////////////////////////////////////////////
	
	public function __clone()
	{
		$this->DataStructure = clone $this->DataStructure;
	}
	
	public function compare($priority1, $priority2) // -> int [\SplPriorityQueue]
	{
		return $priority1 - $priority2;
	}
	
	public function count() // -> int [\Countable]
	{
		return $this->DataStructure->count();
	}
	
	public function current() // -> mixed [\Iterator]
	{
		return $this->processExtract($this->DataStructure->current());
	}
	
	public function extract() // -> mixed [\SplPriorityQueue]
	{
		return $this->processExtract($this->DataStructure->extract());
	}
	
	public function insert($value, $priority) // [\SplPriorityQueue]
	{
		$this->DataStructure->insert(new PriorityQueueHeapItem($value, $priority));
		
		// SplPriorityQueue documentation claims that this function returns void
		// but this does not seem to be the case!
		return true;
	}
	
	public function isEmpty() // -> bool [\SplPriorityQueue]
	{
		return $this->DataStructure->isEmpty();
	}
	
	public function key() // -> mixed [\Iterator]
	{
		return $this->DataStructure->key();
	}
	
	public function next() // [\Iterator]
	{
		$this->DataStructure->next();
	}
	
	public function recoverFromCorruption() // [\SplPriorityQueue]
	{
		// SplPriorityQueue documentation claims that this function returns void
		// but this does not seem to be the case!
		return $this->DataStructure->recoverFromCorruption();
	}
	
	public function rewind() // [\Iterator]
	{
		$this->DataStructure->rewind();
	}
	
	public function setExtractFlags($flags) // [\SplPriorityQueue]
	{
		$this->flags = $flags & PriorityQueue::EXTR_BOTH;
		
		// SplPriorityQueue documentation claims that this function returns void
		// but this does not seem to be the case!
		return $this->flags;
	}
	
	public function top() // -> mixed [\SplPriorityQueue]
	{
		return $this->processExtract($this->DataStructure->top());
	}
	
	public function update($value, $priority)
	{
		$item = new PriorityQueueHeapItem($value, $priority);
		$this->DataStructure->update($item, $item);
	}
	
	public function valid() // -> bool [\Iterator]
	{
		return $this->DataStructure->valid();
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Protected members
	////////////////////////////////////////////////////////////////////////////
	
	protected function processExtract($extract) // -> mixed
	{
		$r = null;
		
		if($extract instanceof PriorityQueueHeapItem)
		{
			if( ($this->flags & PriorityQueue::EXTR_BOTH) === PriorityQueue::EXTR_BOTH)
			{
				$r = array
				(
					'data'     => $extract->value,
					'priority' => $extract->priority
				);
			}
			else if( ($this->flags & PriorityQueue::EXTR_DATA) === PriorityQueue::EXTR_DATA)
			{
				$r = $extract->value;
			}
			else if( ($this->flags & PriorityQueue::EXTR_PRIORITY) === PriorityQueue::EXTR_PRIORITY)
			{
				$r = $extract->priority;
			}
		}
		
		return $r;
	}
}

