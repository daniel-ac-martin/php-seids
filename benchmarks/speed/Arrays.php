<?php
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

define('DS_SIZE',     10000);
define('REPITITIONS',   100);

////////////////////////////////////////////////////////////////////////////////

require 'vendor/autoload.php';

use \SEIDS\Arrays\Dynamic\DynamicArray as DynamicArray;
use \SEIDS\Arrays\Dynamic\ArrayDeque   as ArrayDeque;

function new_DS($name)
{
	switch($name)
	{
		case 'PHP array':
			$return = array();
			break;
		case 'SplFixedArray':
			$return = new SplFixedArray(DS_SIZE);
			break;
		case 'DynamicArray':
			$return = new DynamicArray();
			break;
		case 'ArrayDeque':
			$return = new ArrayDeque();
			break;
	}
	
	return $return;
}

$classes = array('PHP array', 'SplFixedArray', 'DynamicArray', 'ArrayDeque');

////////////////////////////////////////////////////////////////////////////////

echo 'Write & Read:' . "\n";

$times   = array();

// Do the action many times

$m = 0;
$M = REPITITIONS;

while($m++ < $M)
{
	foreach($classes as $class)
	{
		$object = new_DS($class);
		
		$start_time = microtime(true);
		
		$n = 0;
		$N = DS_SIZE;
		
		while($n < $N)
		{
			if($object instanceof SplFixedArray)
			{
				$object[$n] = mt_rand();
			}
			else
			{
				$object[] = mt_rand();
			}
			
			++$n;
		}
		
		$n = 0;
		
		while($n < $N)
		{
			$buffer = $object[$n++];
		}
		
		$times[$class][] = microtime(true) - $start_time;
		
		unset($object);
	}
}

// Take averages and standard deviations
$average = array();

// Calculate means
foreach($times as $i => $v)
{
	foreach($v as $e)
	{
		@$average[$i]['mean'] += $e;
	}
	
	$average[$i]['mean'] /= count($v);
}

// Calculate standard deviations
foreach($times as $i => $v)
{
	foreach($v as $e)
	{
		$diff = $e - $average[$i]['mean'];
		
		@$average[$i]['std_dev'] += $diff * $diff;
	}
	
	$average[$i]['std_dev'] /= $N;
	$average[$i]['std_dev']  = sqrt($average[$i]['std_dev']);
}

// Output results
foreach($average as $i => $v)
{
	echo $i . ': ' . $v['mean'] . ' +/- ' . $v['std_dev'] . "\n";
}

?>
