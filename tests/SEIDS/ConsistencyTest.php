<?php namespace SEIDS;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

abstract class ConsistencyTest extends \PHPUnit_Framework_TestCase
{
	abstract protected function new_DS();
	abstract protected function new_SPL_DS();
	abstract protected function insertDuplicateData(&$a, &$b);
	
	protected $simple_safe_methods = array();
	protected $asymmetric_methods = array();
	
	public function testConstructor()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->assertMethodsEqual($SPL_DS, $the_DS);
	}
	
	public function testBuild()
	{
		$the_DS = $this->new_DS();
		$SPL_DS = $this->new_SPL_DS();
		
		$this->insertDuplicateData($SPL_DS, $the_DS);
		
		$this->assertMethodsEqual($SPL_DS, $the_DS);
	}
	
	public function testSymmetry()
	{
		$SPL_DS  = $this->new_SPL_DS();
		$reflect = new \ReflectionClass($SPL_DS);
		$methods = $reflect->getMethods(\ReflectionMethod::IS_PUBLIC);
		
		foreach($methods as $e)
		{
			$name = $e->name;
			
			if
			(
				   (!in_array($name, $this->asymmetric_methods))
				&& ($name{0} !== '_')
			)
			{
				$the_DS = $this->new_DS();
				$SPL_DS = $this->new_SPL_DS();
				$args   = array();
				$n      = 0;
				$N      = $e->getNumberOfRequiredParameters();
				
				while($n < $N)
				{
					$args[] = ++$n;
				}
				
				$this->assertMethodEqual($SPL_DS, $the_DS, $name, $args);
				$this->insertDuplicateData($SPL_DS, $the_DS);
				$this->assertMethodEqual($SPL_DS, $the_DS, $name, $args);
			}
		}
	}
	
	protected function assertMethodEqual($a, $b, $method, $args = array())
	{
		$res_a = null;
		$res_b = null;
		$exc_a = null;
		$exc_b = null;
		
		$exc_a_str = '';
		$exc_b_str = '';
		
		$arg_str = '';
		$first   = true;
		
		foreach($args as $e)
		{
			if($first)
			{
				$first = false;
			}
			else
			{
				$arg_str .= ', ';
			}
			
			ob_start();
			var_dump($e);
			$arg_str .= trim(ob_get_clean());
		}
		
		$err_str = ' when testing method "' . $method . '(' . $arg_str . ')": ';
		
		try
		{
			$callback = array($a, $method);
			$res_a = call_user_func_array($callback, $args);
		}
		catch(\Exception $exception)
		{
			$exc_a     = $exception;
			$exc_a_str = get_class($exc_a) . ': ' . $exc_a->getMessage();
		}
		
		try
		{
			$callback = array($b, $method);
			$res_b = call_user_func_array($callback, $args);
		}
		catch(\Exception $exception)
		{
			$exc_b     = $exception;
			$exc_b_str = get_class($exc_b) . ': ' . $exc_b->getMessage();
		}
		
		if(null === $exc_a)
		{
			$this->assertNull($exc_b, 'SPL does not throw an exception' . $err_str . 'Received "' . $exc_b_str . '": ');
		}
		else
		{
			$this->assertTrue(is_object($exc_a), 'SPL does not throw an exception' . $err_str . 'Received "' . $exc_a . '": ');
			$this->assertTrue(is_object($exc_b), 'Failed to throw exception' . $err_str . 'Expected "' . $exc_a_str . ': ');
			$this->assertTrue(is_a($exc_b, get_class($exc_a)), 'Wrong exception type' . $err_str . 'Expected "' . $exc_a_str . '", received "'. $exc_b_str . '": ');
			$this->assertEquals($exc_a->getMessage(), $exc_b->getMessage(), 'Different error messages' . $err_str);
		}
		
		$this->assertEquals($res_a, $res_b, 'Different results' . $err_str);
	}
	
	protected function assertMethodsEqual($a, $b)
	{
		foreach($this->simple_safe_methods as $e)
		{
			$this->assertMethodEqual($a, $b, $e);
		}
	}
}

