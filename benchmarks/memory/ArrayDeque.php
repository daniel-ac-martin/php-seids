<?php
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

define('DS_SIZE', 10000);

////////////////////////////////////////////////////////////////////////////////

require 'vendor/autoload.php';

use \SEIDS\Arrays\Dynamic\ArrayDeque as ArrayDeque;

// Take initial memory usage
$initial_memory = memory_get_usage();

// Build data structure
$ds = new ArrayDeque(DS_SIZE);

$n = 0;

while($n < DS_SIZE)
{
	$ds[$n++] = mt_rand();
}

// Calculate memory usage of data structure
$memory_usage = memory_get_usage() - $initial_memory;

// Output results
echo 'Memory usage: ' . $memory_usage . ' bytes' . "\n";

?>
