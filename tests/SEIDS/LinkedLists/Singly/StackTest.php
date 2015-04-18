<?php namespace SEIDS\LinkedLists\Singly;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class StackTest extends \SEIDS\CountableTest
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
		'isEmpty',
		'top'
	);
	protected $asymmetric_methods = array
	(
		'add',
		'current',
		'getIteratorMode',
		'key',
		'next',
		'offsetExists',
		'offsetGet',
		'offsetSet',
		'offsetUnset',
		'prev',
		'rewind',
		'serialize',
		'setIteratorMode',
		'shift',
		'unserialize',
		'unshift',
		'valid'
	);
	
	protected function new_DS()
	{
		return new Stack;
	}
	
	protected function new_SPL_DS()
	{
		return new \SplStack;
	}
	
	protected function insertDuplicateData(&$a, &$b)
	{
		foreach($this->data as $e)
		{
		    $a->push($e);
		    $b->push($e);
		}
	}
	
	public function testBottom()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'bottom');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'bottom');
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
}

