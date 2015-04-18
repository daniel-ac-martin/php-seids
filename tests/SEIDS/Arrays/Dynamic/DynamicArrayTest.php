<?php namespace SEIDS\Arrays\Dynamic;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class DynamicArrayTest extends \SEIDS\ArrayAccessTest
{
	protected $data = array
	(
		'foo',
		'bar',
		'baz',
		'qux',
		'quux'
	);
	protected $simple_safe_methods = array
	(
		'count',
		'current',
		'getSize',
		'key',
		'next',
		'rewind',
	);
	
	protected function new_DS($size = 0)
	{
		return new DynamicArray($size);
	}
	
	protected function new_SPL_DS($size = 0)
	{
		return new \SplFixedArray($size);
	}
	
	protected function insertDuplicateData(&$a, &$b)
	{
		$a->setSize(count($this->data));
		
		$n = 0;
		
		foreach($this->data as $e)
		{
			$a[$n++] = $e;
			$b[]     = $e;
		}
	}
	
    public function testConstructor()
    {
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		$this->assertEquals(0, count($the_DS));
    }
	
    public function testConstructorWithArgument()
    {
		$size   = 69;
		$the_DS = $this->new_DS    ($size);
		$SPL_DS = $this->new_SPL_DS($size);
		
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		$this->assertEquals($size, count($the_DS));
    }
	
	public function testWakeUp()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$the_DS = serialize($the_DS);
		$SPL_DS = serialize($SPL_DS);
		
		$the_DS = unserialize($the_DS);
		$SPL_DS = unserialize($SPL_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'toArray');
	}
	
	public function testFromArray()
	{
		$array = array
		(
			'foo',
			'bar',
			'baz',
			'qux'
		);
		
		$the_DS = DynamicArray::fromArray($array);
		$SPL_DS = \SplFixedArray::fromArray($array);
		
		$this->assertMethodsEqual($SPL_DS, $the_DS);
	}
	
	public function testFromArrayDisordered()
	{
		$array = array
		(
			1 => 'foo',
			3 => 'bar',
			2 => 'baz',
			0 => 'qux'
		);
		
		$the_DS = DynamicArray::fromArray($array);
		$SPL_DS = \SplFixedArray::fromArray($array);
		
		$this->assertMethodsEqual($SPL_DS, $the_DS);
	}
	
	public function testFromArrayAssoc()
	{
		$array = array
		(
			'foo' => 0,
			'bar' => 1,
			'baz' => 2,
			'qux' => 3
		);
		
		$this->assertMethodEqual('\SplFixedArray', __NAMESPACE__ . '\DynamicArray', 'fromArray', array($array));
	}
	
	public function testPush()
	{
		$the_DS = $this->new_DS();
		
		$the_DS->push('first');
		
		foreach($this->data as $e)
		{
			$the_DS[] = $e;
		}
		
		$the_DS->push('last');
		
		$output = '';
		
		foreach($the_DS as $e)
		{
			$output .= $e;
		}
		
		$expect = 'first' . implode($this->data) . 'last';
		
		$this->assertEquals($expect, $output);
	}
	
	public function testPop()
	{
		$the_DS    = $this->new_DS();
		$exception = null;
		
		try
		{
			$the_DS->pop();
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertEquals('Can\'t pop from an empty datastructure', $exception->getMessage());
		
		foreach($this->data as $e)
		{
			$the_DS[] = $e;
		}
		
		$output = '';
		
		while($the_DS->valid())
		{
			$output .= $the_DS->pop();
		}
		
		$expect = implode(array_reverse($this->data));
		
		$this->assertEquals($expect, $output);
	}
	
	public function testGetAllocatedSize()
	{
		$the_DS = $this->new_DS();
		
		$this->assertEquals(0, $the_DS->getAllocatedSize());
		
		foreach($this->data as $e)
		{
			$the_DS[] = $e;
		}
		
		$n = count($the_DS);
		$size = 1;
		
		while($size < $n)
		{
			$size *= 2;
		}
		
		$this->assertEquals($size, $the_DS->getAllocatedSize());
	}
	
	public function testSetSize()
	{
		$the_DS = $this->new_DS();
		
		$size = 69;
		
		$the_DS->setSize($size);
		
		$this->assertEquals($size, $the_DS->getSize());
		
		$SPL_DS = $this->new_SPL_DS();
		$the_DS = $this->new_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setSize', $args = array(-1));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setSize', $args = array('a'));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setSize', $args = array(true));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setSize', $args = array(false));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setSize', $args = array(null));
	}
	
	public function testToArray()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'toArray');
		$this->insertDuplicateData($SPL_DS, $the_DS);
		$this->assertMethodEqual($SPL_DS, $the_DS, 'toArray');
	}
	
	public function testNullOffset()
	{
		$the_DS = $this->new_DS(1);
		
		$the_DS[] = 1;
		$the_DS[] = 2;
		
		$this->assertEquals(array(null, 1, 2), $the_DS->toArray());
	}
}

