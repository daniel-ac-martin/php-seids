<?php namespace SEIDS\Heaps\Binary;
//==============================================================================
// PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
//
// Copyright 2015, Sergei Shilko, Daniel A.C. Martin
// Distributed under the MIT License.
// (See LICENSE file for details.)
//==============================================================================

use \SEIDS\Heaps\PriorityQueueHeapItem;
use \SEIDS\Heaps\UpdateException;

class PriorityQueueSimple extends \SEIDS\Heaps\PriorityQueue
{
	public function __construct() // [\SplPriorityQueue]
	{
		$this->DataStructure = new PriorityQueueHeapSimple();
	}

    /**
     * Inserts a new value or update existing with new priority
     * (does not makes heap items unique if it is already non-unique)
     *
     * @param $value
     * @param $priority
     */
    public function set($value, $priority)
    {
        try {
            return $this->update($value, $priority);
        } catch (UpdateException $e) {
            return $this->insert($value, $priority);
        }
    }

    /**
	 * Convert into array (to preserve state/serialize)
	 *
	 * @param $array
	 */
	public function toArray()
	{
		return $this->DataStructure->toArray();
	}

	public function toJson()
	{
		return json_encode($this->toArray());
	}

	/**
	 * Restore from array (from state/unserialize)
	 * Comparator function is not restored
	 *
	 * @param $array
	 */
	public function fromArray($array)
	{
		$this->DataStructure->fromArray($array);
	}


	public function fromJson($string) {
		$this->fromArray(json_decode($string));
	}

}