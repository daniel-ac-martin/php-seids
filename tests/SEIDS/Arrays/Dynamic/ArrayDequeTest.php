<?php namespace SEIDS\Arrays\Dynamic;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class ArrayDequeTest extends DynamicArrayTest
{
	protected function new_DS($size = 0)
	{
		return new ArrayDeque($size);
	}
	
	protected function new_SPL_DS($size = 0)
	{
		return new \SplFixedArray($size);
	}
	
	public function testUnshift()
	{
		$the_DS = $this->new_DS();
		
		$the_DS->unshift('second');
		
		foreach($this->data as $e)
		{
			$the_DS[] = $e;
		}
		
		$the_DS->unshift('first');
		
		$output = '';
		
		foreach($the_DS as $e)
		{
			$output .= $e;
		}
		
		$expect = 'firstsecond' . implode($this->data);
		
		$this->assertEquals($expect, $output);
	}
	
	public function testShift()
	{
		$the_DS    = $this->new_DS();
		$exception = null;
		
		try
		{
			$the_DS->shift();
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertEquals('Can\'t shift from an empty datastructure', $exception->getMessage());
		
		foreach($this->data as $e)
		{
			$the_DS[] = $e;
		}
		
		$the_DS->unshift('foo');
		$the_DS->unshift('foo');
		$the_DS->unshift('foo');
		$the_DS->shift();
		$the_DS->shift();
		$the_DS->shift();
		
		$output = '';
		
		while($the_DS->valid())
		{
			$output .= $the_DS->shift();
		}
		
		$expect = implode($this->data);
		
		$this->assertEquals($expect, $output);
	}
}

