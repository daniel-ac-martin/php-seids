<?php namespace SEIDS\LinkedLists\Singly;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class Item
{
	public $data = null;
	public $next = null;
	
	public function __construct($data, Item $next = null)
	{
		$this->data = $data;
		$this->next = $next;
	}
	
	public function __clone()
	{
		if(is_object($this->data))
		{
			$this->data = clone $this->data;
		}
	}
}

