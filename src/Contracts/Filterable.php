<?php

namespace Stylemix\Base\Contracts;

interface Filterable
{

	/**
	 * Apply filter query
	 *
	 * @param \Illuminate\Support\Collection $filters
	 */
	public function applyFilter($filters);

}
