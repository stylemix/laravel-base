<?php

namespace Stylemix\Base;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Stylemix\Base\Attributes\BaseAttribute;
use Stylemix\Base\Contracts\Filterable;
use Stylemix\Base\Contracts\Searchable;
use Stylemix\Base\Contracts\Sortable;

class AttributeCollection extends Collection
{

	/**
	 * All key variations (name, fills, sorts)
	 *
	 * @return \Stylemix\Base\AttributeCollection
	 */
	public function allKeys()
	{
		return $this->keys()
			->merge($this->map->fills()->flatten())
			->merge($this->map->sorts()->flatten())
			->unique();
	}

	/**
	 * Get attributes keyed by fills
	 *
	 * @return \Stylemix\Base\AttributeCollection
	 */
	public function keyByFills()
	{
		$byFills = new static();

		$this->each(function (BaseAttribute $attribute) use ($byFills) {
			foreach (Arr::wrap($attribute->fills()) as $key) {
				$byFills->put($key, $attribute);
			}
		});

		return $byFills;
	}

	/**
	 * Attributes mapped with all key variations (name, fills, sorts)
	 *
	 * @return \Stylemix\Base\AttributeCollection
	 */
	public function keyByAll()
	{
		return $this
			->merge($this->keyByFills())
			->merge($this->implementsSortable());
	}

	/**
	 * Find attribute by all key variations (name, fills, sorts)
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function find($key)
	{
		return $this->keyByAll()->get($key);
	}

	/**
	 * Get attributes that can be filled
	 *
	 * @return \Stylemix\Base\AttributeCollection|BaseAttribute[]
	 */
	public function fillable()
	{
		return $this->where('fillable', '=', true);
	}

	/**
	 * @return \Stylemix\Base\AttributeCollection|Filterable[]|BaseAttribute[]
	 */
	public function implementsFiltering()
	{
		return $this->whereInstanceOf(Filterable::class);
	}

	/**
	 * Get attributes that implement sorting keyed by sorting attributes
	 *
	 * @return \Stylemix\Base\AttributeCollection|Sortable[]|BaseAttribute[]
	 */
	public function implementsSortable()
	{
		$result = $this->make();

		$this->whereInstanceOf(Sortable::class)->each(function ($attribute) use ($result) {
			foreach ($attribute->sorts() as $key) {
				$result->put($key, $attribute);
			}
		});

		return $result;
	}

	/**
	 * Get attributes that implement searching
	 *
	 * @return \Stylemix\Base\AttributeCollection|Sortable[]|BaseAttribute[]
	 */
	public function implementsSearching()
	{
		return $this->whereInstanceOf(Searchable::class);
	}
}
