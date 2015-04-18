<?php namespace SEIDS\Heaps;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

abstract class HeapTest extends \SEIDS\IteratorTest
{
	abstract protected function new_Heap($array = null);
	abstract protected function new_SplHeap();
	
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
		
		while($n++ < 99)
		{
			$this->data[] = rand();
		}
		
		parent::__construct();
	}
	
	protected function new_DS()
	{
		return $this->new_Heap();
	}
	
	protected function new_SPL_DS()
	{
		return $this->new_SplHeap();
	}
	
	protected function insertDuplicateData(&$a, &$b)
	{
		foreach($this->data as $e)
		{
		    $a->insert($e);
		    $b->insert($e);
		}
	}
	
	protected function buildArray()
	{
		return $this->data;
	}
	
	public function testInsertExtract()
	{
		$array   = $this->buildArray();
		$theSplH = $this->new_SplHeap();
		$theHeap = $this->new_Heap();
	 	
		foreach($array as $e)
		{
		    $theSplH->insert($e);
		    $theHeap->insert($e);
		}
		
		$this->assertEquals(count($theSplH), count($theHeap));
		
		while($theSplH->valid() || $theHeap->valid())
		{
			$this->assertEquals($theSplH->extract(), $theHeap->extract());
		}
	}
	
	public function testConstructorExtract()
	{
		$array   = $this->buildArray();
		$theSplH = $this->new_SplHeap();
		$theHeap = $this->new_Heap($array);
	 	
		foreach($array as $e)
		{
		    $theSplH->insert($e);
		}
		
		$n = 0;
		$N = count($array);
		
		$this->assertEquals($N, count($theSplH));
		$this->assertEquals($N, count($theHeap));
		
		while($n++ < $N)
		{
			$this->assertEquals($theHeap->extract(), $theSplH->extract());
		}
	}
	
	public function testConstructorBadArgument()
	{
		$exception = null;
		
		try
		{
			$theHeap = $this->new_Heap('foo');
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
	 	
		$this->assertEquals('Argument to constructor of Heap must be an array, string given', $exception->getMessage());
	}
	
	public function testClone()
	{
		$array   = $this->buildArray();
		$theHeap = $this->new_Heap();
	 	
		foreach($array as $e)
		{
		    $theHeap->insert($e);
		}
		
		$theHeapClone = clone $theHeap;
		
		while($theHeap->valid() || $theHeapClone->valid())
		{
			$this->assertEquals($theHeap->extract(), $theHeapClone->extract());
		}
	}
	
	public function testCloneWithObjects()
	{
		$array   = $this->buildArray();
		$theHeap = $this->new_Heap();
	 	
		$theHeap->insert(new \stdClass); // For code coverage
		$theHeap->extract();             // To avoid comparison on \stdClass
		
		foreach($array as $e)
		{
			$theHeap->insert(new \SplInt($e));
			$theHeap->insert(new \SplFloat((float)$e));
			$theHeap->insert(new \SplBool((bool)$e));
			$theHeap->insert(new \SplString((string)$e));
		}
		
		$theHeapClone = clone $theHeap;
		
		while($theHeap->valid() || $theHeapClone->valid())
		{
			$this->assertEquals($theHeap->extract(), $theHeapClone->extract());
		}
	}
	
	public function testForeach()
	{
		$array   = $this->buildArray();
		$theSplH = $this->new_SplHeap();
		$theHeap = $this->new_Heap();
	 	
		foreach($array as $e)
		{
		    $theSplH->insert($e);
		    $theHeap->insert($e);
		}
		
		$output_spl = '';
		
		foreach($theSplH as $e)
		{
			$output_spl .= $e;
		}
		
		$output = '';
		
		foreach($theHeap as $e)
		{
			$output .= $e;
		}
		
		$this->assertEquals($output, $output_spl);
	}
	
	public function testUpdateUnupdate()
	{
		$array = $this->buildArray();
		
		$array[] = 5;
		$array[] = 3;
		$array[] = 1;
		$array[] = 2;
		$array[] = 7;
		$array[] = 3;
		$array[] = 100;
		$array[] = 42;
		
		$theHeap = $this->new_Heap();
		
		$theSplH = $this->new_SplHeap();
		$theHeap = $this->new_Heap();
	 	
		foreach($array as $e)
		{
		    $theSplH->insert($e);
		    $theHeap->insert($e);
		}
		
		$theHeap->update(42, 10042);
		$theHeap->update(10042, 42);
		
		$output_spl = '';
		
		foreach($theSplH as $e)
		{
			$output_spl .= $e;
		}
		
		$output = '';
		
		foreach($theHeap as $e)
		{
			$output .= $e;
		}
		
		$this->assertEquals($output, $output_spl);
	}
	
	public function testRecoverFromCorruption()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'recoverFromCorruption');
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'recoverFromCorruption');
	}
	
	public function testUpdate()
	{
		$array2 = array
		(
			4,
			10,
			8,
			2,
			7,
			5,
			1,
			3,
			6,
			9
		);
		
		$theHeap = $this->new_Heap();
	 	
		foreach($array2 as $e)
		{
		    $theHeap->insert($e);
		}
		
		$theHeap->update(7, 2);
		$theHeap->update(3, 9);
		
		$array2 = array
		(
			4,
			10,
			8,
			2,
			2,
			5,
			1,
			9,
			6,
			9
		);
		
		$theSplH = $this->new_SplHeap();
		
		foreach($array2 as $e)
		{
		    $theSplH->insert($e);
		}
		
		while($theSplH->valid() || $theHeap->valid())
		{
			$this->assertEquals($theSplH->extract(), $theHeap->extract());
		}
	}
	
	public function testUpdateVsInsert()
	{
		$array2 = array
		(
			4,
			10,
			8,
			2,
			7,
			5,
			1,
			3,
			6,
			9
		);
		
		$theHeap = $this->new_Heap();
	 	
		foreach($array2 as $e)
		{
		    $theHeap->insert($e);
		}
		
		$theHeapClone = clone $theHeap;
		
		$theHeap->update(7, 2);
		
		$newHeapClone = $this->new_Heap();
		
		foreach($theHeapClone as $e)
		{
			if($e === 7)
			{
				$e = 2;
			}
			
			$newHeapClone->insert($e);
		}
		
		$theHeapClone = $newHeapClone;
		
		while($theHeap->valid() || $theHeapClone->valid())
		{
			$this->assertEquals($theHeap->extract(), $theHeapClone->extract());
		}
	}
	
	public function testUpdateExtra()
	{
		$array = array
		(
			6,
			15,
			10,
			17,
			7,
			10
		);
		
		$theHeap = $this->new_Heap();
	 	
		foreach($array as $e)
		{
		    $theHeap->insert($e);
		}
		
		$theHeapClone = clone $theHeap;
		
		$theHeap->update(6, 20);
		$theHeap->update(17, 1);
		$theHeap->update(7, 13);
		$theHeap->update(15, 3);
		$theHeap->update(10, 11);
		$theHeap->update(10, 11);
		
		$theHeapClone->update(15, 3);
		$theHeapClone->update(10, 11);
		$theHeapClone->update(17, 1);
		$theHeapClone->update(7, 13);
		$theHeapClone->update(10, 11);
		$theHeapClone->update(6, 20);
		
		while($theHeap->valid() || $theHeapClone->valid())
		{
			$this->assertEquals($theHeap->extract(), $theHeapClone->extract());
		}
	}
	
	public function testUpdateNotFound()
	{
		$theHeap   = $this->new_Heap();
		$exception = null;
		
		try
		{
			$theHeap->update(6, 20);
		}
		catch(\Exception $e)
		{
			$exception = $e;
		}
		
		$this->assertEquals('Unable to find element in heap', $exception->getMessage());
	}
	
	public function testAllOps()
	{
		$theSplH = $this->new_SplHeap();
		$theHeap = $this->new_Heap();
		$N       = 49;
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->recoverFromCorruption(), $theHeap->recoverFromCorruption());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		srand(0);
		
		$n = 0;
		while($n++ < $N)
		{
			$e = rand();
			
		    $theHeap->insert($e);
		    $theSplH->insert($e);
		}
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->extract(), $theHeap->extract());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->recoverFromCorruption(), $theHeap->recoverFromCorruption());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->top(), $theHeap->top());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->extract(), $theHeap->extract());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->recoverFromCorruption(), $theHeap->recoverFromCorruption());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->top(), $theHeap->top());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		$n = 0;
		while($n++ < $N)
		{
			$e = rand();
			
		    $theHeap->insert($e);
		    $theSplH->insert($e);
		}
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->extract(), $theHeap->extract());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->recoverFromCorruption(), $theHeap->recoverFromCorruption());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->top(), $theHeap->top());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->extract(), $theHeap->extract());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->recoverFromCorruption(), $theHeap->recoverFromCorruption());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->top(), $theHeap->top());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		while($theSplH->valid())
		{
			$this->assertEquals($theSplH->count(), $theHeap->count());
			$this->assertEquals($theSplH->current(), $theHeap->current());
			$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
			$this->assertEquals($theSplH->key(), $theHeap->key());
			$this->assertEquals($theSplH->recoverFromCorruption(), $theHeap->recoverFromCorruption());
			$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
			$this->assertEquals($theSplH->top(), $theHeap->top());
			$this->assertEquals($theSplH->valid(), $theHeap->valid());
			$this->assertEquals($theSplH->extract(), $theHeap->extract());
			$this->assertEquals($theSplH->next(), $theHeap->next());
		}
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->recoverFromCorruption(), $theHeap->recoverFromCorruption());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->recoverFromCorruption(), $theHeap->recoverFromCorruption());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
	}
	
	public function testAllOpsNoRecovery()
	{
		$theSplH = $this->new_SplHeap();
		$theHeap = $this->new_Heap();
		$N       = 49;
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		srand(0);
		
		$n = 0;
		while($n++ < $N)
		{
			$e = rand();
			
		    $theHeap->insert($e);
		    $theSplH->insert($e);
		}
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->extract(), $theHeap->extract());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->top(), $theHeap->top());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->extract(), $theHeap->extract());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->top(), $theHeap->top());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		$n = 0;
		while($n++ < $N)
		{
			$e = rand();
			
		    $theHeap->insert($e);
		    $theSplH->insert($e);
		}
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->extract(), $theHeap->extract());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->top(), $theHeap->top());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->extract(), $theHeap->extract());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->top(), $theHeap->top());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		while($theSplH->valid())
		{
			$this->assertEquals($theSplH->count(), $theHeap->count());
			$this->assertEquals($theSplH->current(), $theHeap->current());
			$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
			$this->assertEquals($theSplH->key(), $theHeap->key());
			$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
			$this->assertEquals($theSplH->top(), $theHeap->top());
			$this->assertEquals($theSplH->valid(), $theHeap->valid());
			$this->assertEquals($theSplH->extract(), $theHeap->extract());
			$this->assertEquals($theSplH->next(), $theHeap->next());
		}
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
		
		$this->assertEquals($theSplH->count(), $theHeap->count());
		$this->assertEquals($theSplH->current(), $theHeap->current());
		$this->assertEquals($theSplH->isEmpty(), $theHeap->isEmpty());
		$this->assertEquals($theSplH->key(), $theHeap->key());
		$this->assertEquals($theSplH->next(), $theHeap->next());
		$this->assertEquals($theSplH->rewind(), $theHeap->rewind());
		$this->assertEquals($theSplH->valid(), $theHeap->valid());
	}
}

