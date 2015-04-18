<?php namespace SEIDS\LinkedLists\Singly;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class QueueTest extends LinkedListTest
{
	protected function new_DS()
	{
		return new Queue;
	}
	
	protected function new_SPL_DS()
	{
		return new \SplQueue;
	}
	
	public function testEnqueue()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'enqueue', array('foo'));
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'enqueue', array('bar'));
	}
	
	public function testDequeue()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'dequeue');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'dequeue');
	}
}

