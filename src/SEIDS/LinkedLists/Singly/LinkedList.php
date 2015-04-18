<?php namespace SEIDS\LinkedLists\Singly;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

use \SEIDS\LinkedLists\CantPeekAtEmptyException;
use \SEIDS\LinkedLists\CantPopFromEmptyException;
use \SEIDS\LinkedLists\CantShiftFromEmptyException;
use \SEIDS\LinkedLists\ModeNotSupportedException;
use \SEIDS\LinkedLists\OutOfRangeException;
use \SEIDS\LinkedLists\UnserializeException;

class LinkedList implements \ArrayAccess, \Countable, \Iterator
{
	////////////////////////////////////////////////////////////////////////////
	// Members
	////////////////////////////////////////////////////////////////////////////
	
	const IT_MODE_DELETE = 1;
	const IT_MODE_KEEP   = 0;
	const IT_MODE_LIFO   = 2;
	const IT_MODE_FIFO   = 0;
	
	protected $first       = null; // &Item
	protected $last        = null; // &Item
	protected $current     = null; // &Item
	protected $current_key = null; // int
	protected $worker      = null; // &Item
	protected $worker_key  = null; // int
	protected $size        = 0;    // int
	protected $flags       = 0;    // int
	
	////////////////////////////////////////////////////////////////////////////
	// Public methods - Implement the same interface as \SplDoublyLinkedList
	//                  without support for LIFO.
	////////////////////////////////////////////////////////////////////////////
	
	public function __construct() // [\SplDoublyLinkedList]
	{
	}
	
	public function __clone()
	{
		if(null !== $this->first)
		{
			$this->first = clone $this->first;
			
			$e = $this->first;
			
			while(null !== $e->next)
			{
				$e->next = clone $e->next;
				$e       = $e->next;
			}
			
			$this->last = $e;
		}
	}
	
	// Warning: This is slow (O(n)).
	public function add($index, $newval) // [\SplDoublyLinkedList]
	{
		$first = $index === 0;
		$last  = $index === $this->size;
		
		if($first)
		{
			$this->unshift($newval);
		}
		else if($last)
		{
			$this->push($newval);
		}
		else if($this->offsetExists($index))
		{
			$this->moveToOffset($index - 1);
			
			$prev = $this->worker;
			
			$item = new Item($newval, $prev->next);
			
			$prev->next = $item;
			
			++$this->size;
			
			if
			(
				   (null !== $this->current_key)
				&& ($index <= $this->current_key)
			)
			{
				++$this->current_key;
			}
		}
		else
		{
			throw new OutOfRangeException('Offset invalid or out of range');
		}
	}
	
	public function bottom() // -> mixed [\SplDoublyLinkedList]
	{
		$r = null;
		
		if($this->isEmpty())
		{
			throw new CantPeekAtEmptyException('Can\'t peek at an empty datastructure');
		}
		else
		{
			$r = $this->first->data;
		}
		
		return $r;
	}
	
	public function count() // -> int [\Countable]
	{
		return $this->size;
	}
	
	public function current() // -> mixed [\Iterator]
	{
		return ($this->current instanceof Item) ? $this->current->data
		                                        : null;
	}
	
	public function getIteratorMode() // -> int [\SplDoublyLinkedList]
	{
		return $this->flags;
	}
	
	public function isEmpty() // -> bool [\SplDoublyLinkedList]
	{
		return 0 === $this->size;
	}
	
	public function key() // -> mixed [\Iterator]
	{
		return (null === $this->current_key) ? 0
		                                     : $this->current_key;
	}
	
	// Warning: This is slower moving down than up (O(2n) vs O(n)).
	public function move($offset_from, $offset_to)
	{
		if
		(
			   $this->offsetExists($offset_from)
			&& $this->offsetExists($offset_to)
		)
		{
			$this->add($offset_to, $this->remove($offset_from));
		}
		else
		{
			throw new OutOfRangeException('Offset invalid or out of range');
		}
	}
	
	public function next() // [\Iterator]
	{
		if($this->flags & LinkedList::IT_MODE_DELETE)
		{
			if($this->valid())
			{
				$this->remove($this->current_key); // O(1) despite appearances (unless prev() has been called)
			}
		}
		else
		{
			$this->worker     = $this->current;
			$this->worker_key = $this->current_key;
			
			if(null !== $this->current)
			{
				$this->current = $this->current->next;
				++$this->current_key;
			}
		}
	}
	
	public function offsetExists($index) // -> bool [\ArrayAccess]
	{
		return is_integer($index) && (0 <= $index) && ($index < $this->size);
	}
	
	// Warning: This is slow (O(n)).
	public function offsetGet($index) // -> mixed [\ArrayAccess]
	{
		$r = null;
		
		if($this->offsetExists($index))
		{
			$this->moveToOffset($index);
			
			$r = $this->worker->data;
		}
		else
		{
			throw new OutOfRangeException('Offset invalid or out of range');
		}
		
		return $r;
	}
	
	// Warning: This is slow (O(n)).
	public function offsetSet($index, $newval) // [\ArrayAccess]
	{
		if($this->offsetExists($index))
		{
			$this->moveToOffset($index);
			
			$this->worker->data = $newval;
		}
		else if($index == '')
		{
			$this->push($newval);
		}
		else
		{
			throw new OutOfRangeException('Offset invalid or out of range');
		}
	}
	
	// Warning: This is slow (O(n)).
	public function offsetUnset($index) // [\ArrayAccess]
	{
		if($this->offsetExists($index))
		{
			$this->remove($index);
		}
		else
		{
			throw new OutOfRangeException('Offset out of range');
		}
	}
	
	// Warning: This is slow (O(n)).
	public function pop() // -> mixed [\SplDoublyLinkedList]
	{
		$r = null;
		
		if($this->isEmpty())
		{
			throw new CantPopFromEmptyException('Can\'t pop from an empty datastructure');
		}
		else
		{
			$r = $this->remove($this->size - 1);
		}
		
		return $r;
	}
	
	public function prev() // [\SplDoublyLinkedList]
	{
		if
		(
			   (null !== $this->current)
			&& (0 < $this->current_key)
		)
		{
			$key = $this->current_key - 1;
			
			$this->moveToOffset($key);
			
			$this->current = $this->worker;
			$this->current_key = $key;
		}
	}
	
	public function push($value) // [\SplDoublyLinkedList]
	{
		$item = new Item($value);
		
		if(0 < $this->size)
		{
			$this->last->next = $item;
		}
		else
		{
			$this->first = $item;
		}
		
		$this->last = $item;
		
		++$this->size;
		
		// SplDoublyLinkedList documentation claims that this function returns
		// void but this does not seem to be the case!
		return true;
	}
	
	// Warning: This is slow (O(n)).
	public function remove($offset) // -> mixed
	{
		$return = null;
		
		if($this->offsetExists($offset))
		{
			if(0 === $offset)
			{
				$return = $this->shift();
			}
			else
			{
				$this->moveToOffset($offset - 1);
				
				$prev   = $this->worker;
				$pos    = $prev->next;
				$return = $pos->data;
				$last   = $offset === $this->size - 1;
				
				if($last)
				{
					$prev->next = null;
					$this->last = $prev;
				}
				else
				{
					$prev->next = $pos->next;
				}
				
				unset($pos);
				
				--$this->size;
				
				if(null !== $this->current_key)
				{
					if($this->current_key === $offset)
					{
						$this->current = $prev->next;
					}
					else if($this->current_key > $offset)
					{
						--$this->current_key;
					}
				}
			}
		}
		else
		{
			throw new OutOfRangeException('Offset invalid or out of range');
		}
		
		return $return;
	}
	
	public function rewind() // [\Iterator]
	{
		$this->current     = $this->first;
		$this->current_key = (null === $this->first) ? null
		                                             : 0;
	}
	
	// Warning: Undocumented in \SplDoublyLinkedList
	public function serialize() // -> string [\SplDoublyLinkedList]
	{
		return serialize($this->first);
	}
	
	public function setIteratorMode($mode) // [\SplDoublyLinkedList]
	{
		if($mode & LinkedList::IT_MODE_LIFO)
		{
			throw new ModeNotSupportedException('LIFO mode not supported by LinkedList');
		}
		
		$this->flags = $mode & LinkedList::IT_MODE_DELETE;
		
		// SplDoublyLinkedList documentation claims that this function returns
		// void but this does not seem to be the case!
		return $this->flags;
	}
	
	public function shift() // -> mixed [\SplDoublyLinkedList]
	{
		$return = null;
		
		if($this->isEmpty())
		{
			throw new CantShiftFromEmptyException('Can\'t shift from an empty datastructure');
		}
		else
		{
			$pos         = $this->first;
			$this->first = $pos->next;
			$return      = $pos->data;
		
			unset($pos);
		
			--$this->size;
		
			if(0 === $this->size)
			{
				$this->last = null;
			}
			
			if(0 === $this->current_key)
			{
				$this->current = $this->first;
				
				if(null === $this->current)
				{
					$this->current_key = null;
				}
			}
			else if(null !== $this->current_key)
			{
				--$this->current_key;
			}
			
			if(0 === $this->worker_key)
			{
				$this->worker = $this->first;
				
				if(null === $this->worker)
				{
					$this->worker_key = null;
				}
			}
			else if(null !== $this->worker_key)
			{
				--$this->worker_key;
			}
		}
		
		return $return;
	}
	
	public function top() // -> mixed [\SplDoublyLinkedList]
	{
		$r = null;
		
		if($this->isEmpty())
		{
			throw new CantPeekAtEmptyException('Can\'t peek at an empty datastructure');
		}
		else
		{
			$r = $this->last->data;
		}
		
		return $r;
	}
	
	// Warning: Undocumented in \SplDoublyLinkedList
	public function unserialize($serialized) // [\SplDoublyLinkedList]
	{
		try
		{
			if(null === $this->last)
			{
				$this->first = unserialize($serialized);
			}
			else
			{
				$this->last->next = unserialize($serialized);
			}
		}
		catch(\Exception $e)
		{
			$error = $e->getMessage();
			
			throw new UnserializeException(substr($error, strpos($error, ': ') + 2));
		}
		
		$n    = 0;
		$iter = $this->first;
		$last = null;
		
		while(null !== $iter)
		{
			$last = $iter;
			$iter = $iter->next;
			++$n;
		}
		
		$this->last = $last;
		$this->size = $n;
	}
	
	public function unshift($value) // [\SplDoublyLinkedList]
	{
		$item = new Item($value, $this->first);
		
		$this->first = $item;
		
		++$this->size;
		
		if(1 === $this->size)
		{
			$this->last = $item;
		}
		
		if(null !== $this->current_key)
		{
			++$this->current_key;
		}
		
		if(null !== $this->worker_key)
		{
			++$this->worker_key;
		}
		
		// SplDoublyLinkedList documentation claims that this function returns
		// void but this does not seem to be the case!
		return true;
	}
	
	public function valid() // -> bool [\Iterator]
	{
		return $this->current_key !== null && $this->current_key < $this->size;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Protected methods
	////////////////////////////////////////////////////////////////////////////
	
	protected function moveToOffset($offset)
	{
		if($offset === $this->size - 1)
		{
			$this->worker     = $this->last;
			$this->worker_key = $this->size - 1;
		}
		else
		{
			$ahead_of_worker  = ($offset >= $this->worker_key)  && (null !== $this->worker);
			$ahead_of_current = ($offset >= $this->current_key) && (null !== $this->current);
			$ahead_of_both    = $ahead_of_worker && $ahead_of_current;
			
			if
			(
				   ($ahead_of_worker && !$ahead_of_both)
				|| ($ahead_of_both && ($this->worker_key > $this->current_key) )
			)
			{
			}
			else if($ahead_of_current)
			{
				$this->worker     = $this->current;
				$this->worker_key = $this->current_key;
			}
			else
			{
				$this->worker     = $this->first;
				$this->worker_key = 0;
			}
		}
	
		while($this->worker_key < $offset)
		{
			$this->worker = $this->worker->next;
			++$this->worker_key;
		}
	}
}

