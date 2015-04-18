<?php namespace SEIDS\Heaps\Pairing;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class SubheapTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$n = 0;
		
		$parent    = new Subheap($n, $n++);
		$child_one = new Subheap($n, $n++);
		$child_two = new Subheap($n, $n++);
		
		$children = array
		(
			$child_one->tag => $child_one,
			$child_two->tag => $child_two
		);
		
		$subheap = new Subheap($n, $n++, $children, $parent);
		
		$parent->subheaps[$subheap->tag] = $subheap;
		$child_one->parent               = $subheap;
		$child_two->parent               = $subheap;
		
		$this->assertEquals(0, $parent->data);
		$this->assertEquals(1, $child_one->data);
		$this->assertEquals(2, $child_two->data);
		$this->assertEquals(3, $subheap->data);
		
		$this->assertEquals($subheap, $child_one->parent);
		$this->assertEquals($subheap, $child_two->parent);
		$this->assertEquals($parent,  $subheap  ->parent);
		
		$this->assertEquals($parent ->subheaps[$subheap  ->tag], $subheap);
		$this->assertEquals($subheap->subheaps[$child_one->tag], $child_one);
		$this->assertEquals($subheap->subheaps[$child_two->tag], $child_two);
	}
}

