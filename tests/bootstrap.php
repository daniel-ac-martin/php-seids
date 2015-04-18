<?php namespace SEIDS;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

require __DIR__ . '/../src/autoload.php';

$loader = new SplClassLoader('SEIDS', __DIR__);
$loader->register();
