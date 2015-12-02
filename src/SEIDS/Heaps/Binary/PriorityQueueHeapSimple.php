<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
//
// Copyright 2015, Sergei Shilko, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

use \SEIDS\Heaps\PriorityQueueHeapItem;
use \SEIDS\Heaps\Binary\Item;

class PriorityQueueHeapSimple extends Heap
{
	/**
	 * @param $value1
	 * @param $value2
	 *
	 * @return mixed
	 */
	protected function compare($value1, $value2)
	{
		return $value1->priority - $value2->priority;
	}

    protected function value($value)
    {
        return $value->value;
    }

	/**
	 * Convert into array (to preserve state/serialize)
	 *
	 * @param $array
	 */
	public function toArray()
	{
		$array = $this->btree;
		array_shift($array);
        $array = array_map(function($i) {
            return array($i->data->priority,
                         $i->data->value);}, $array);
		return $array;
	}

	/**
	 * Restore from array (from state/unserialize)
	 *
	 * @param $array
	 */
	public function fromArray($array)
	{
        $array = array_map(function($i) {
			$c = new \stdClass();
			$c->data = new PriorityQueueHeapItem($i[1], $i[0]);
			return $c;
        }, $array);

		array_unshift($array, null);
		$this->btree = $array;

		/**
		 * No actual siftUp is performed and it should take O(heapHeight)=O(log n) to fix the heap
		 */
		$this->recoverFromCorruption();
	}
}

