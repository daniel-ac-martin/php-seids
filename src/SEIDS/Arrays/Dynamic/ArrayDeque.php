<?php namespace SEIDS\Arrays\Dynamic;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

use \SEIDS\Arrays\CantShiftFromEmptyException;

class ArrayDeque extends DynamicArray
{
	////////////////////////////////////////////////////////////////////////////
	// Members
	////////////////////////////////////////////////////////////////////////////
	
	protected $start       = 0; // int
	protected $current_key = 0; // int
	
	////////////////////////////////////////////////////////////////////////////
	// Public methods - Same interface as DynamicArray but with ability to
	//                  shift and unshift.
	////////////////////////////////////////////////////////////////////////////
	
	public function __construct($size = 0) // [\SplFixedArray]
	{
		$this->start = ($size instanceof \SplFixedArray) ? 0
		                                                 : $size / 2;
		parent::__construct($size);
	}
	
	public function current() // -> mixed [\Iterator]
	{
		return $this->data->offsetGet($this->map($this->current_key));
	}
	
	public function key() // -> int [\Iterator]
	{
		return $this->current_key;
	}
	
	public function next() // [\Iterator]
	{
		if($this->valid())
		{
			++$this->current_key;
		}
	}
	
	public function offsetExists($index) // -> bool [\ArrayAccess]
	{
		return parent::offsetExists($this->map($index));
	}
	
	public function offsetGet($index) // -> mixed [\ArrayAccess]
	{
		return parent::offsetGet($this->map($index));
	}
	
	public function offsetSet($index, $newval) // [\ArrayAccess]
	{
		parent::offsetSet($this->map($index), $newval);
	}
	
	public function offsetUnset($index) // [\ArrayAccess]
	{
		parent::offsetUnset($this->map($index));
	}
	
	public function rewind() // [\Iterator]
	{
		$this->current_key = 0;
	}
	
	public function shift() // -> mixed
	{
		$r = null;
		
		if($this->isEmpty())
		{
			throw new CantShiftFromEmptyException('Can\'t shift from an empty datastructure');
		}
		else
		{
			$n = 0;
			$r = $this->offsetGet($n);
		
			$this->offsetUnset($n);
			
			--$this->count;
			++$this->start;
			
			$size = $this->data->getSize();
			
			if($size <= $this->start)
			{
				$this->start -= $size;
			}
			
			$this->shrink();
		}
		
		return $r;
	}
	
	public function toArray() // -> array [\SplFixedArray]
	{
		$r     = null;
		$array = $this->data->toArray();
		
		if($this->map(0) < $this->map($this->count - 1))
		{
			$r = array_slice($array, $this->map(0), $this->count);
		}
		else
		{
			$r = array_merge
			(
				array_slice($array, $this->map(0)),
				array_slice($array, 0, $this->map($this->count))
			);
		}
		
		return $r;
	}
	
	public function unshift($value)
	{
		$this->grow();
		
		++$this->count;
		--$this->start;
		
		if(0 > $this->start)
		{
			$this->start += $this->data->getSize();
		}
		
		$this->offsetSet(0, $value);
	}
	
	public function valid() // -> bool [\Iterator]
	{
		return (0 <= $this->current_key) && ($this->current_key < ($this->count) );
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Protected methods
	////////////////////////////////////////////////////////////////////////////
	
	protected function map($index) // -> int
	{
		$r = $index;
		
		if( (null !== $index) && is_integer($index))
		{
			$r    = $index + $this->start;
			$size = $this->data->getSize();
			
			if($r >= $size)
			{
				$r -= $size;
			}
		}
		
		return $r;
	}
	
	protected function offsetValid($index) // -> bool [DynamicArray]
	{
		$size = $this->data->getSize();
		
		return
		(
			   ($this->start <= $index)
			&& ($index < ($this->start + $this->count) )
			&& ($index < ($size) )
		) || (
			   (0 <= $index)
			&& ($index < $this->start + $this->count - $size)
		);
	}
	
	protected function resize($size, $count = null) // [DynamicArray]
	{
		$data  = new \SplFixedArray($size);
		$count = (null === $count) ? $this->count : $count;
		$start = (int)(($size - $count) / 2);
		$n     = 0;
		$N     = ($count < $this->count) ? $count : $this->count;
		
		while($n < $N)
		{
			$data->offsetSet($n + $start, $this->data->offsetGet($this->map($n)));
			++$n;
		}
		
		$this->data  = $data;
		$this->start = $start;
		$this->count = $count;
	}
}

