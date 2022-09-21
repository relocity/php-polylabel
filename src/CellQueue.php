<?php

namespace Relocity\PhpPolylabel;

use Relocity\PhpPolylabel\Cell;
use SplPriorityQueue;

class CellQueue {

	public $splPriorityQueue;

    public function __construct()
    {
		$this->splPriorityQueue = new SplPriorityQueue();	
	}

	public function push(Cell $cell)
    {
		$this->splPriorityQueue->insert($cell, $cell->max);
	}

	public function length()
    {
		return $this->splPriorityQueue->count();
	}

	/** @return Cell */
	public function pop()
    {
		return $this->splPriorityQueue->extract();
	}
} 