<?php namespace SEIDS;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

abstract class IteratorTest extends CountableTest
{
	public function testForeach()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$res_a = array();
		$res_b = array();
		
		foreach($SPL_DS as $e)
		{
			array_push($res_a, $e);
		}
		
		foreach($the_DS as $e)
		{
			array_push($res_b, $e);
		}
		
		$this->assertEquals($res_a, $res_b);
	}
}

