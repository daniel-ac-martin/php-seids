<?php namespace SEIDS\Heaps\Pairing;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

use \SEIDS\Heaps\ConstructorException;
use \SEIDS\Heaps\ExtractException;
use \SEIDS\Heaps\PeekException;
use \SEIDS\Heaps\UpdateException;

abstract class Heap extends \SEIDS\Heaps\Heap
{
	////////////////////////////////////////////////////////////////////////////
	// Members
	////////////////////////////////////////////////////////////////////////////
	
	protected $subheap  = null;    // &Subheap
	protected $hasht    = array(); // Array(mixed => Array(int => &Subheap))
	protected $last_tag = 0;       // int
	
	////////////////////////////////////////////////////////////////////////////
	// Public methods - Implement the same interface as \SplHeap but with the
	//                  ability to update.
	////////////////////////////////////////////////////////////////////////////
	
	public function __construct($array = null) // [\SplHeap]
	{
		if($array !== null)
		{
			if(is_array($array))
			{
				// Sort for faster extraction
				usort($array, array($this, 'compare'));
				
				foreach($array as $e)
				{
					$this->insert($e);
				}
			}
			else
			{
				throw new ConstructorException('Argument to constructor of Heap must be an array, ' . gettype($array) . ' given');
			}
		}
	}
	
	public function __clone()
	{
		$this->subheap = clone $this->subheap;
		$this->hasht[$this->hashtIndex($this->subheap->data)][$this->subheap->tag] = $this->subheap;
		
		$stack = array($this->subheap);
		
		while(null !== ($e = array_pop($stack)) )
		{
			if(is_object($e->data))
			{
				$e->data = clone $e->data;
			}
			
			foreach($e->subheaps as $i => $v)
			{
				$clone                                        = clone $v;
				$e->subheaps[$i]                              = $clone;
				$this->hasht[$this->hashtIndex($v->data)][$i] = $clone;
				$clone->parent                                = $e;
				
				array_push($stack, $clone);
			}
		}
	}
	
	public function extract() // -> mixed [\SplHeap]
	{
		$r = null;
		
		if($this->valid())
		{
			$r = $this->top();
			$v = $this->hashtIndex($r);
			
			unset($this->hasht[$v][$this->subheap->tag]);
			
			if(empty($this->hasht[$v]))
			{
				unset($this->hasht[$v]);
			}
						
			$this->mergePairs($this->subheap);
			
			$this->subheap = empty($this->subheap->subheaps) ? null
			                                                 : array_values($this->subheap->subheaps)[0];
			
			if(null !== $this->subheap)
			{
				$this->subheap->parent = null;
			}
			
			--$this->size;
		}
		else
		{
			throw new ExtractException('Can\'t extract from an empty heap');
		}
		
		return $r;
	}
	
	public function insert($value) // [\SplHeap]
	{
		$v                   = $this->hashtIndex($value);
		$t                   = ++$this->last_tag;
		$e                   = new Subheap($value, $t);
		$this->hasht[$v][$t] = $e;
		
		$this->subheap = $this->size ? $this->merge($this->subheap, $e)
		                             : $e;
		
		++$this->size;
		
		// SplHeap documentation claims that this function returns void but this
		// does not seem to be the case!
		return true;
	}
	
	// Not entirely sure what this function is supposed to do.
	public function recoverFromCorruption() // [\SplHeap]
	{
		// Recover as much data as possible ignoring order
		$data = array();
		
		foreach($this as $e)
		{
			$data[] = $e;
		}
		
		// Reset properties
		$this->size     = 0;
		$this->subheap  = null;
		$this->hasht    = array();
		$this->last_tag = 0;
		
		// Re-insert data
		foreach($data as $e)
		{
			$this->insert($e);
		}
		
		// SplHeap documentation claims that this function returns void but this
		// does not seem to be the case!
		return true;
	}
	
	public function top() // -> mixed [\SplHeap]
	{
		$r = null;
		
		if($this->valid())
		{
			$r = $this->subheap->data;
		}
		else
		{
			throw new PeekException('Can\'t peek at an empty heap');
		}
		
		return $r;
	}
	
	public function update($value1, $value2) // [\SEIDS\Heaps\AbstractHeap]
	{
		$v = $this->hashtIndex($value1);
		
		if(empty($this->hasht[$v]))
		{
			throw new UpdateException('Unable to find element in heap');
		}
		else
		{
			$tag     = array_keys($this->hasht[$v])[0];
			$subheap = $this->hasht[$v][$tag];
			
			$subheap->data = $value2;
			
			unset($this->hasht[$v][$tag]);
			
			if(empty($this->hasht[$v]))
			{
				unset($this->hasht[$v]);
			}
			
			$this->hasht[$this->hashtIndex($value2)][$tag] = $subheap;
			
			$this->siftUp($subheap);
			$this->siftDown($subheap);
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Protected methods
	////////////////////////////////////////////////////////////////////////////
	
	protected function merge(Subheap $a, Subheap $b) // -> &Subheap
	{
		$p = $b;
		$c = $a;
		
		if(0 < $this->compare($a->data, $b->data))
		{
			$p = $a;
			$c = $b;
		}
		
		$p->subheaps[$c->tag] = $c;
		$c->parent            = $p;
		
		return $p;
	}
	
	protected function mergePairs(Subheap $subheap)
	{
		if(!empty($subheap->subheaps))
		{
			$first_pass = array();
			$buffer     = null;
			
			foreach($subheap->subheaps as $e)
			{
				if(null === $buffer)
				{
					$buffer = $e;
				}
				else
				{
					$first_pass[] = $this->merge($buffer, $e);
					$buffer       = null;
				}
			}
			
			if(null !== $buffer)
			{
				$first_pass[] = $buffer;
				$buffer       = null;
			}
			
			$first_pass = array_reverse($first_pass);
			
			foreach($first_pass as $e)
			{
				if(null === $buffer)
				{
					$buffer = $e;
				}
				else
				{
					$buffer = $this->merge($buffer, $e);
				}
			}
			
			$subheap->subheaps = array($buffer->tag => $buffer);
		}
	}
	
	protected function siftDown(Subheap $subheap)
	{
		$this->mergePairs($subheap);
		
		if(!empty($subheap->subheaps))
		{
			$child = array_values($subheap->subheaps)[0];
			
			if(0 < $this->compare($child->data, $subheap->data))
			{
				$this->swapWithParent($child);
			}
		}
	}
	
	protected function siftUp(Subheap $subheap)
	{
		$not_done = true;
		
		while
		(
			   $not_done
			&& (null !== $subheap->parent)
		)
		{
			if(0 < $this->compare($subheap->data, $subheap->parent->data))
			{
				$this->swapWithParent($subheap);
			}
			else
			{
				$not_done = false;
			}
		}
	}
	
	protected function swapWithParent($child)
	{
		$parent = $child->parent;
		
		unset($parent->subheaps[$child->tag]);
		
		$child->subheaps[$parent->tag] = $parent;
		
		if(null !== $parent->parent)
		{
			unset($parent->parent->subheaps[$parent->tag]);
			
			$parent->parent->subheaps[$child->tag] = $child;
		}
		else
		{
			$this->subheap = $child;
		}
		
		$child->parent  = $parent->parent;
		$parent->parent = $child;
	}
}

