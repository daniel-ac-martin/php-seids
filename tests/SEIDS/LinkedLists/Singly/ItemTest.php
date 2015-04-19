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
		$n     = 5;
		$three = null;
		
		if(class_exists('SplInt')) $four  = new Item(new \SplInt(--$n));
		if(class_exists('SplInt')) $three = new Item(new \SplInt(--$n), $four);
		
		$two = new Item(--$n, $three);
		$one = new Item(--$n, $two);
		
		$one_clone = clone $one;
		
		$this->assertEquals($one->data, $one_clone->data);
		$this->assertEquals($one->next, $one_clone->next);
		
		$this->assertEquals($one->next->data, $one_clone->next->data);
		
		$one->data = 5;
		$two->data = 6;
		
		$this->assertEquals(1, $one_clone->data);
		$this->assertEquals(6, $one_clone->next->data);
		
		if(class_exists('SplInt'))
		{
			$one->data = new \SplInt(7);
			$two->data = new \SplInt(8);
		
			$this->assertEquals(1, (int)$one_clone->data);
			$this->assertEquals(8, (int)$one_clone->next->data);
		}
	}
}

