<?php namespace SEIDS\Arrays\Dynamic;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

use \SEIDS\Arrays\CantPopFromEmptyException;
use \SEIDS\Arrays\InvalidArgumentException;
use \SEIDS\Arrays\InvalidIndexException;

class DynamicArray implements \ArrayAccess, \Countable, \Iterator
{
	////////////////////////////////////////////////////////////////////////////
	// Members
	////////////////////////////////////////////////////////////////////////////
	
	protected $data  = null; // &\SplFixedArray
	protected $count = 0;    // int
	
	////////////////////////////////////////////////////////////////////////////
	// Public methods
	////////////////////////////////////////////////////////////////////////////
	
	public function __construct($size = 0) // [\SplFixedArray]
	{
		$this->data  = ($size instanceof \SplFixedArray) ? $size
		                                                 : new \SplFixedArray($size);
		$this->count = ($size instanceof \SplFixedArray) ? $this->data->getSize()
		                                                 : $size;
	}
	
	public function __clone()
	{
		$this->data = clone $this->data;
	}
	
	public function count() // -> int [\Countable]
	{
		return $this->count;
	}
	
	public function current() // -> mixed [\Iterator]
	{
		return $this->data->current();
	}
	
	public static function fromArray($array, $save_indexes = true) // -> DynamicArray [\SplFixedArray]
	{
		return new DynamicArray(\SplFixedArray::fromArray($array, $save_indexes));
	}
	
	public function getAllocatedSize() // -> int
	{
		return $this->data->getSize();
	}
	
	public function getSize() // -> int [\SplFixedArray]
	{
		return $this->count();
	}
	
	public function isEmpty() // -> bool
	{
		return 0 === $this->count;
	}
	
	public function key() // -> int [\Iterator]
	{
		return $this->data->key();
	}
	
	public function next() // [\Iterator]
	{
		if($this->valid())
		{
			$this->data->next();
		}
	}
	
	public function offsetExists($index) // -> bool [\ArrayAccess]
	{
		return (!$this->offsetValid($index)) ? false : $this->data->offsetExists($index);
	}
	
	public function offsetGet($index) // -> mixed [\ArrayAccess]
	{
		$r = null;
		
		if($this->offsetValid($index))
		{
			$r = $this->data->offsetGet($index);
		}
		else
		{
			throw new InvalidIndexException('Index invalid or out of range');
		}
		
		return $r;
	}
	
	public function offsetSet($index, $newval) // [\ArrayAccess]
	{
		if(null === $index)
		{
			$this->push($newval);
		}
		else if($this->offsetValid($index))
		{
			$this->data->offsetSet($index, $newval);
		}
		else
		{
			throw new InvalidIndexException('Index invalid or out of range');
		}
	}
	
	public function offsetUnset($index) // [\ArrayAccess]
	{
		if($this->offsetValid($index))
		{
			$this->data->offsetUnset($index);
		}
		else
		{
			throw new InvalidIndexException('Index invalid or out of range');
		}
	}
	
	public function pop() // -> mixed
	{
		$r = null;
		
		if($this->isEmpty())
		{
			throw new CantPopFromEmptyException('Can\'t pop from an empty datastructure');
		}
		else
		{
			$n = $this->count - 1;
			$r = $this->offsetGet($n);
		
			$this->offsetUnset($n);
			
			--$this->count;
			$this->shrink();
		}
		
		return $r;
	}
	
	public function push($value)
	{
		$this->grow();
		
		$this->offsetSet($this->count++, $value);
	}
	
	public function rewind() // [\Iterator]
	{
		$this->data->rewind();
	}
	
	public function setSize($size) // -> int [\SplFixedArray]
	{
		if(true === $size)
		{
			$size = 1;
		}
		else if(false === $size)
		{
			$size = 0;
		}
		else if(null === $size)
		{
			$size = 0;
		}
		
		if(!is_numeric($size))
			trigger_error('SplFixedArray::setSize() expects parameter 1 to be long, ' . gettype($size) . ' given', E_USER_WARNING); // No braces to workaround code coverage bug.
		else if($size < 0)
		{
			throw new InvalidArgumentException('array size cannot be less than zero');
		}
		else
		{
			$this->resize($size, $size);
		}
		
		return 1;
	}
	
	public function toArray() // -> array [\SplFixedArray]
	{
		return array_slice($this->data->toArray(), 0, $this->count);
	}
	
	public function valid() // -> bool [\Iterator]
	{
		return ($this->data->key() < $this->count) ? $this->data->valid()
		                                           : false;
	}
	
	public function __wakeup() // [\SplFixedArray]
	{
		$this->data->__wakeup();
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Protected methods
	////////////////////////////////////////////////////////////////////////////
	
	protected function grow()
	{
		$size = $this->data->getSize();
		
		if($size === $this->count)
		{
			$size = (0 < $size) ? 2 * $size
				                : 1;
			
			$this->resize($size);
		}
	}
	
	protected function offsetValid($index) // -> bool
	{
		return (0 <= $index) && ($index < $this->count);
	}
	
	protected function resize($size, $count = null)
	{
		$count = (null === $count) ? $this->count : $count;
		
		$this->data->SetSize($size);
		
		$n = $count;
		
		while($n < $size)
		{
			$this->data->offsetUnset($n++);
		}
		
		$this->count = $count;
	}
	
	protected function shrink()
	{
		$new_size = $this->data->getSize() / 2;
		
		if($this->count < ($new_size / 2) )
		{
			$this->resize($new_size);
		}
	}
}

