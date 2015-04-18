<?php namespace SEIDS\Heaps\Pairing;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
// 
// Copyright 2015, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

class Subheap
{
	public $data;               // mixed
	public $tag;                // int
	public $parent   = null;    // &Subheap
	public $subheaps = array(); // Array(&Subheap)
	
	public function __construct($data, $tag, $subheaps = null, Subheap $parent = null)
	{
		$this->data   = $data;
		$this->tag    = $tag;
		$this->parent = $parent;
		
		if
		(
			   ($subheaps !== null)
			&& (is_array($subheaps))
		)
		{
			$this->subheaps = $subheaps;
		}
	}
}
