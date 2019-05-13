<?php

namespace Stylemix\Base\Contracts;

use Stylemix\Base\Entity;

interface Mutatable
{

	/**
	 * Return mutated value when requesting attribute
	 *
	 * @param \Stylemix\Base\Entity $model
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getterMutator(Entity $model, $key);

	/**
	 * Sets mutated value when updating attribute value
	 *
	 * @param \Stylemix\Base\Entity $model
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function setterMutator(Entity $model, $key, $value);
}
