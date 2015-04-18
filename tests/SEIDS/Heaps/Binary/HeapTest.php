<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

abstract class HeapTest extends \SEIDS\Heaps\HeapTest
{
	public function testRecoverFromCorruption()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'recoverFromCorruption');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'recoverFromCorruption');
		
		// FUBAR the heap!
		$exception = null;
		$reflect   = new \ReflectionClass($the_DS);
		$property  = $reflect->getProperty('btree');
		
		$property->setAccessible(true);
		$property->setValue($the_DS, array('FUBAR!'));
		
		try
		{
			$the_DS->recoverFromCorruption();
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertTrue(is_object($exception), 'Failed to throw exception.');
		$this->assertEquals('SEIDS\Heaps\RecoverException', get_class($exception));
	}
	
	public function testExtractInsert()
	{
		$the_DS = $this->new_DS();
		
		foreach($this->data as $e)
		{
		    $the_DS->insert($e);
		}
		
		$the_DS_clone = clone $the_DS;
		$n            = 0;
		
		while($n++ < 20)
		{
			$insert = mt_rand();
			
			$a = $the_DS_clone->extract();
			$the_DS_clone->insert($insert);
			
			$b = $the_DS->extractInsert($insert);
			
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
			$the_DS->extractInsert(0);
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertTrue(is_object($exception), 'Failed to throw exception.');
		$this->assertEquals('SEIDS\Heaps\ExtractException', get_class($exception));
	}
}

