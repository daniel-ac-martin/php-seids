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

use \SEIDS\Heaps\Binary\MaxHeap  as BinaryMaxHeap;
use \SEIDS\Heaps\Pairing\MaxHeap as PairingMaxHeap;

function new_DS($name)
{
	switch($name)
	{
		case 'SplMaxHeap':
			$return = new SplMaxHeap();
			break;
		case 'BinaryMaxHeap':
			$return = new BinaryMaxHeap();
			break;
		case 'PairingMaxHeap':
			$return = new PairingMaxHeap();
			break;
	}
	
	return $return;
}

$classes = array('SplMaxHeap', 'BinaryMaxHeap', 'PairingMaxHeap');

////////////////////////////////////////////////////////////////////////////////

echo 'Insert & Extract:' . "\n";

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
		
		while($n++ < $N)
		{
			$object->insert(mt_rand());
		}
		
		$n = 0;
		
		while($n++ < $N)
		{
			$object->extract();
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
