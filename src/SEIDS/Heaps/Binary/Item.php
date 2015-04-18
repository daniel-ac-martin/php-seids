<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class Item
{
	public $data;
	public $tag;
	
	public function __construct($data, $tag)
	{
		$this->data = $data;
		$this->tag  = $tag;
	}
}
