<?php namespace SEIDS;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

abstract class ArrayAccessTest extends IteratorTest
{
	public function testArrayAccess()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetExists', array(4));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetExists', array(5));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet', array(4));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet', array(5));
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetSet', array(3, 'BAZ'));
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet', array(3));
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetUnset', array(3));
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet', array(3));
	}
	
	public function testClone()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$the_DS_clone = clone $the_DS;
		$SPL_DS_clone = clone $SPL_DS;
		
		$the_DS[2] = 'BAZ';
		$SPL_DS[2] = 'BAZ';
		
		$the_DS_clone[3] = 'QUX';
		$SPL_DS_clone[3] = 'QUX';
		
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
		
		$res_a = array();
		$res_b = array();
		
		foreach($SPL_DS_clone as $e)
		{
			array_push($res_a, $e);
		}
		
		foreach($the_DS_clone as $e)
		{
			array_push($res_b, $e);
		}
		
		$this->assertEquals($res_a, $res_b);
	}
	
	public function testOutOfRange()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$offset = count($the_DS) + 1;
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetExists', array($offset));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet',    array($offset));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetUnset',  array($offset));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetSet',    array($offset, 'foo'));
	}
	
	public function testNullOffset()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetExists', array(null));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet',    array(null));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetUnset',  array(null));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetSet',    array(null, 'foo'));
	}
	
	public function testStringOffset()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$offset = 'some random string';
		
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetExists', array($offset));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetGet',    array($offset));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetUnset',  array($offset));
		$this->assertMethodEqual($SPL_DS, $the_DS, 'offsetSet',    array($offset, 'foo'));
	}
}

