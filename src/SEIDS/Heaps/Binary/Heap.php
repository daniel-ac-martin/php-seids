<?php namespace SEIDS\Heaps\Binary;
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
use \SEIDS\Heaps\RecoverException;
use \SEIDS\Heaps\UpdateException;

abstract class Heap extends \SEIDS\Heaps\Heap
{
	////////////////////////////////////////////////////////////////////////////
	// Members
	////////////////////////////////////////////////////////////////////////////
	
	protected $btree    = array(null); // Array(int => Item)
	protected $hasht    = array();     // Array(mixed => Array(int => int))
	protected $last_tag = 0;           // int
	
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
				// Heapify & tag
				$this->btree = $array;
				$first       = true;
				
				array_unshift($this->btree, null);
				
				foreach($this->btree as $i => $e)
				{
					if($first)
					{
						$first = false;
					}
					else
					{
						$v                   = $this->hashtIndex($e);
						$t                   = ++$this->last_tag;
						$this->hasht[$v][$t] = $i;
						$this->btree[$i]     = new Item($e, $t);
						
						$this->siftUp(++$this->size);
					}
				}
			}
			else
			{
				throw new ConstructorException('Argument to constructor of Heap must be an array, ' . gettype($array) . ' given');
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
			
			unset($this->hasht[$v][$this->btree[1]->tag]);
			
			if(empty($this->hasht[$v]))
			{
				unset($this->hasht[$v]);
			}
			
			$bottom = array_pop($this->btree);
			
			--$this->size;
			
			if($this->size > 0)
			{
				$this->btree[1]                                              = $bottom;
				$this->hasht[$this->hashtIndex($bottom->data)][$bottom->tag] = 1;
				
				$this->siftDown(1);
			}
		}
		else
		{
			throw new ExtractException('Can\'t extract from an empty heap');
		}
		
		return $r;
	}
	
	public function extractInsert($value) // -> mixed
	{
		$r = null;
		
		if($this->valid())
		{
			$r = $this->top();
			$v = $this->hashtIndex($r);
			
			unset($this->hasht[$v][$this->btree[1]->tag]);
			
			if(empty($this->hasht[$v]))
			{
				unset($this->hasht[$v]);
			}
			
			$v                   = $this->hashtIndex($value);
			$t                   = ++$this->last_tag;
			$this->hasht[$v][$t] = 1;
			$this->btree[1]      = new Item($value, $t);
			
			$this->siftDown(1);
		}
		else
		{
			throw new ExtractException('Can\'t extract from an empty heap');
		}
		
		return $r;
	}
	
	public function insert($value) // [\SplHeap]
	{
		++$this->size;
		
		$v                   = $this->hashtIndex($value);
		$t                   = ++$this->last_tag;
		$this->hasht[$v][$t] = $this->size;
		
		array_push($this->btree, new Item($value, $t));
		$this->siftUp($this->size);
		
		// SplHeap documentation claims that this function returns void but this
		// does not seem to be the case!
		return true;
	}
	
	// Not entirely sure what this function is supposed to do.
	public function recoverFromCorruption() // [\SplHeap]
	{
		if(null !== array_shift($this->btree))
		{
			throw new RecoverException('Unable to recover heap');
		}
		else
		{
			// Re-heapify & re-tag
			$this->size     = 0;
			$this->hasht    = array();
			$this->last_tag = 0;
			$first          = true;
			
			array_unshift($this->btree, null);
			
			foreach($this->btree as $i => $e)
			{
				if($first)
				{
					$first = false;
				}
				else
				{
					$v                    = $this->hashtIndex($e->data);
					$t                    = ++$this->last_tag;
					$this->hasht[$v][$t]  = $i;
					$this->btree[$i]->tag = $t;
					
					$this->siftUp(++$this->size);
				}
			}
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
			$r = $this->btree[1]->data;
		}
		else
		{
			throw new PeekException('Can\'t peek at an empty heap');
		}
		
		return $r;
	}
	
	// I believe this runs in O(log(n))
	public function update($value1, $value2) // [\SEIDS\Heaps\AbstractHeap]
	{
		$v = $this->hashtIndex($value1);
		
		if(empty($this->hasht[$v]))
		{
			throw new UpdateException('Unable to find element in heap');
		}
		else
		{
			$t = array_keys($this->hasht[$v])[0];
			$i = $this->hasht[$v][$t];
			
			$this->btree[$i] = new Item($value2, $t);
			
			unset($this->hasht[$v][$t]);
			
			if(empty($this->hasht[$v]))
			{
				unset($this->hasht[$v]);
			}
			
			$this->hasht[$this->hashtIndex($value2)][$t] = $i;
			
			$this->siftUp($i);
			$this->siftDown($i);
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Protected methods
	////////////////////////////////////////////////////////////////////////////
	
	protected function parentIndex($index)
	{
		$r = false;
		
		if($index > 1)
		{
			$r = floor($index / 2);
		}
		else if($index === 1)
		{
			$r = null;
		}
		
		return $r;
	}
	
	protected function childOneIndex($index)
	{
		$r = 2 * $index;
		
		if($r > $this->size)
		{
			$r = null;
		}
		
		return $r;
	}
	
	protected function childTwoIndex($index)
	{
		$r = 2 * $index + 1;
		
		if($r > $this->size)
		{
			$r = null;
		}
		
		return $r;
	}
	
	protected function swap($a, $b)
	{
		$elem_a = $this->btree[$a];
		$elem_b = $this->btree[$b];
		
		$this->hasht[$this->hashtIndex($elem_a->data)][$elem_a->tag] = $b;
		$this->hasht[$this->hashtIndex($elem_b->data)][$elem_b->tag] = $a;
		
		$this->btree[$a] = $elem_b;
		$this->btree[$b] = $elem_a;
	}
	
	protected function siftDown($index)
	{
		$not_done = true;
		
		while($not_done)
		{
			$largest         = $index;
			$child_one_index = $this->childOneIndex($index);
			$child_two_index = $this->childTwoIndex($index);
			
			if
			(
				   (null !== $child_one_index)
				&& (0 < $this->compare($this->btree[$child_one_index]->data, $this->btree[$largest]->data))
			)
			{
				$largest = $child_one_index;
			}
			
			if
			(
				   (null !== $child_two_index)
				&& (0 < $this->compare($this->btree[$child_two_index]->data, $this->btree[$largest]->data))
			)
			{
				$largest = $child_two_index;
			}
			
			if($largest !== $index)
			{
				$this->swap($largest, $index);
				
				$index = $largest;
			}
			else
			{
				$not_done = false;
			}
		}
	}
	
	protected function siftUp($index)
	{
		while(0 < ($parent_index = $this->parentIndex($index)) )
		{
			if(0 < $this->compare($this->btree[$index]->data, $this->btree[$parent_index]->data))
			{
				$this->swap($parent_index, $index);
				
				$index = $parent_index;
			}
			else
			{
				$index = 1;
			}
		}
	}
}

