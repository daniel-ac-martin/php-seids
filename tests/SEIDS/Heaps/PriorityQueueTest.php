<?php namespace SEIDS\Heaps;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

abstract class PriorityQueueTest extends \SEIDS\IteratorTest
{
	protected $data                = array();
	protected $simple_safe_methods = array
	(
		'count',
		'current',
		'key',
		'recoverFromCorruption',
		'rewind',
		'valid'
	);
	
	public function __construct()
	{
		srand(0);
		
		$n = 0;
		
		while($n++ < 16)
		{
			$this->data[] = array(rand(), rand());
		}
		
		parent::__construct();
	}
	
	protected function new_SPL_DS()
	{
		return new \SplPriorityQueue();
	}
	
	protected function insertDuplicateData(&$a, &$b)
	{
		foreach($this->data as $e)
		{
			$a->insert($e[0], $e[1]);
			$b->insert($e[0], $e[1]);
		}
	}
	
	public function testClone()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($the_DS, $SPL_DS);
		
		$the_DS_clone = clone $the_DS;
		$SPL_DS_clone = clone $SPL_DS;
		
		while
		(
			   $the_DS      ->valid()
			|| $the_DS_clone->valid()
			|| $SPL_DS      ->valid()
			|| $SPL_DS_clone->valid()
		)
		{
			$the_extract       = $the_DS      ->extract();
			$the_clone_extract = $the_DS_clone->extract();
			$SPL_extract       = $SPL_DS      ->extract();
			$SPL_clone_extract = $SPL_DS_clone->extract();
			
			$this->assertEquals($the_extract,       $the_clone_extract);
			$this->assertEquals($SPL_extract,       $the_extract);
			$this->assertEquals($SPL_clone_extract, $the_clone_extract);
		}
	}
	
	public function testExtract()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($the_DS, $SPL_DS);
		
		while($SPL_DS->valid() || $the_DS->valid())
		{
			$this->assertMethodEqual($SPL_DS, $the_DS, 'extract');
			$this->assertMethodsEqual($SPL_DS, $the_DS);
		}
	}
	
	public function testSetExtractFlags()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setExtractFlags', array(PriorityQueue::EXTR_BOTH));
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		$this->assertMethodEqual($SPL_DS, $the_DS, 'extract');
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setExtractFlags', array(PriorityQueue::EXTR_DATA));
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		$this->assertMethodEqual($SPL_DS, $the_DS, 'extract');
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'setExtractFlags', array(PriorityQueue::EXTR_PRIORITY));
		$this->assertMethodsEqual($SPL_DS, $the_DS);
		$this->assertMethodEqual($SPL_DS, $the_DS, 'extract');
		$this->assertMethodsEqual($SPL_DS, $the_DS);
	}
	
	public function testUpdate()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($the_DS, $SPL_DS);
		
		$the_DS->insert('updated', 180);
		$SPL_DS->insert('updated', 42);
		$the_DS->update('updated', 42);
		
		while($SPL_DS->valid() || $the_DS->valid())
		{
			$this->assertMethodEqual($SPL_DS, $the_DS, 'extract');
			$this->assertMethodsEqual($SPL_DS, $the_DS);
		}
	}
}

