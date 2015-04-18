<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class PriorityQueueTest extends \SEIDS\Heaps\PriorityQueueTest
{
	protected function new_DS()
	{
		return new PriorityQueue;
	}
	
	public function testExtractInsert()
	{
		$the_DS = $this->new_DS();
		
		foreach($this->data as $e)
		{
		    $the_DS->insert($e[0], $e[1]);
		}
		
		$the_DS_clone = clone $the_DS;
		$n            = 0;
		
		while($n++ < 20)
		{
			$insert_v = mt_rand();
			$insert_p = mt_rand();
			
			$a = $the_DS_clone->extract();
			$the_DS_clone->insert($insert_v, $insert_p);
			
			$b = $the_DS->extractInsert($insert_v, $insert_p);
			
			$this->assertEquals($a, $b);
			$this->assertMethodsEqual($the_DS_clone, $the_DS);
		}
		
		while($the_DS->valid() || $the_DS_clone->valid())
		{
			$this->assertMethodEqual ($the_DS_clone, $the_DS, 'extract');
			$this->assertMethodsEqual($the_DS_clone, $the_DS);
		}
	}
	
	public function testExtractInsertException()
	{
		$the_DS    = $this->new_DS();
		$exception = null;
		
		try
		{
			$the_DS->extractInsert(0, 0);
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertTrue(is_object($exception), 'Failed to throw exception.');
		$this->assertEquals('SEIDS\Heaps\ExtractException', get_class($exception));
	}
}

