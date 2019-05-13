<?php

namespace Stylemix\Base\Contracts;

interface Sortable
{

	/**
	 * Apply query builder allowed sorts
	 *
	 * @param \Illuminate\Support\Collection $sorts Current allowed sorts
	 */
	public function applySort($sorts) : void;
}
