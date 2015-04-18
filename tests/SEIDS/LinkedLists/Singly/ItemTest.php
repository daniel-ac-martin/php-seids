<?php namespace SEIDS\LinkedLists\Singly;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class ItemTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$n = 3;
		
		$two = new Item(--$n);
		$one = new Item(--$n, $two);
		
		$this->assertEquals(1, $one->data);
		$this->assertEquals(2, $two->data);
		
		$this->assertEquals($two, $one->next);
		$this->assertNull($two->next);
	}
	
	public function testClone()
	{
		$n = 3;
		
		$two = new Item(new \SplInt(--$n));
		$one = new Item(new \SplInt(--$n), $two);
		
		$one_clone = clone $one;
		
		$this->assertEquals($one->data, $one_clone->data);
		$this->assertEquals($one->next, $one_clone->next);
		
		$this->assertEquals($one->next->data, $one_clone->next->data);
		
		$one->data = new \SplInt(3);
		$two->data = new \SplInt(4);
		
		$this->assertEquals(1, (int)$one_clone->data);
		$this->assertEquals(4, (int)$one_clone->next->data);
	}
}

