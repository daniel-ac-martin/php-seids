<?php namespace SEIDS\LinkedLists\Singly;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class LinkedListTest extends \SEIDS\ArrayAccessTest
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
		'bottom',
		'count',
		'current',
		'getIteratorMode',
		'isEmpty',
		'key',
		'top',
		'valid'
	);
	protected $asymmetric_methods = array
	(
		'serialize',
		'unserialize'
	);
	
	protected function new_DS()
	{
		return new LinkedList;
	}
	
	protected function new_SPL_DS()
	{
		return new \SplDoublyLinkedList;
	}
	
	protected function insertDuplicateData(&$a, &$b)
	{
		foreach($this->data as $e)
		{
		    $a->push($e);
		    $b->push($e);
		}
	}
	
	public function testEmptyList()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'top');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'bottom');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'pop');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'shift');
	}
	
	public function testAdd()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'add', array(0, 'one'));
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'add', array(1, 'two'));
		
		foreach($the_DS as $e);
		foreach($SPL_DS as $e);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'add', array(2, 'three'));
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'add', array(1, 'one and a half'));
		
		$N = count($SPL_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'add', array($N + 1, 'invalid'));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'add', array($N,     'push!'));
	}
	
	public function testBottom()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'bottom');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'bottom');
	}
	
	public function testMove()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$the_DS->move(1, 3);
		
		$SPL_extract = $SPL_DS[1];
		unset($SPL_DS[1]);
		$SPL_DS->add(3, $SPL_extract);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		while($SPL_DS->valid() || $the_DS->valid())
		{
			$this->assertMethodsEqual($SPL_DS, $the_DS);
			$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		}
		
		$exception = null;
		
		try
		{
			$the_DS->move(1, -1);
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertEquals('SEIDS\LinkedLists\OutOfRangeException', get_class($exception));
	}
	
	public function testOffsetUnsetCoverage()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetUnset', array(1));
	}
	
	public function testPrev()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'prev');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'prev');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'prev');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'prev');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'prev');
	}
	
	public function testRemove()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$the_extract = $the_DS->remove(1);
		
		$SPL_extract = $SPL_DS[1];
		unset($SPL_DS[1]);
		
		$this->assertEquals($SPL_extract, $the_extract);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		while($SPL_DS->valid() || $the_DS->valid())
		{
			$this->assertMethodsEqual($SPL_DS, $the_DS);
			$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		}
		
		$the_extract = $the_DS->remove(1);
		
		$SPL_extract = $SPL_DS[1];
		unset($SPL_DS[1]);
		
		$this->assertEquals($SPL_extract, $the_extract);
		
		$exception = null;
		
		try
		{
			$the_DS->remove(-1);
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertEquals('SEIDS\LinkedLists\OutOfRangeException', get_class($exception));
	}
	
	public function testSerializeUnserialize()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$serialized = $the_DS->serialize();
		$the_DS->unserialize($serialized);
		
		$serialized = $SPL_DS->serialize();
		$SPL_DS->unserialize($serialized);
		
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		while($SPL_DS->valid() || $the_DS->valid())
		{
			$this->assertMethodsEqual($SPL_DS, $the_DS);
			$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		}
		
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$serialized = $the_DS->serialize();
		$the_DS = $this->new_DS();
		$the_DS->unserialize($serialized);
		
		$serialized = $SPL_DS->serialize();
		$SPL_DS = $this->new_SPL_DS();
		$SPL_DS->unserialize($serialized);
		
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		while($SPL_DS->valid() || $the_DS->valid())
		{
			$this->assertMethodsEqual($SPL_DS, $the_DS);
			$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		}
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'unserialize', array('JUNK'));
	}
	
	public function testTop()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'top');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'top');
	}
	
	public function testPush()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'push', array('foo'));
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'push', array('bar'));
	}
	
	public function testPop()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'pop');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'pop');
	}
	
	public function testShift()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'shift');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'shift');
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet', array(count($SPL_DS) - 2));
		
		while(!$SPL_DS->isEmpty() || !$the_DS->isEmpty())
		{
			$this->assertMethodEqual($SPL_DS, $the_DS, 'shift');
		}
	}
	
	public function testUnshift()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'unshift', array('foo'));
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'unshift', array('bar'));
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet', array(2));
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'unshift', array('baz'));
	}
	
	public function testSetIteratorMode()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setIteratorMode', array(0));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setIteratorMode', array(1));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setIteratorMode', array(4));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setIteratorMode', array(5));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setIteratorMode', array(8));
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setIteratorMode', array(LinkedList::IT_MODE_DELETE));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		$this->assertMethodEqual($SPL_DS, $the_DS, 'current');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'rewind');
		while($SPL_DS->valid() || $the_DS->valid())
		{
			$this->assertMethodsEqual($SPL_DS, $the_DS);
			$this->assertMethodEqual($SPL_DS, $the_DS, 'next');
		}
		
		$exception = null;
		
		try
		{
			$the_DS->setIteratorMode(LinkedList::IT_MODE_LIFO);
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertEquals('SEIDS\LinkedLists\ModeNotSupportedException', get_class($exception));
	}
}

