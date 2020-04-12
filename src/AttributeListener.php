<?php

namespace Stylemix\Base;

class AttributeListener
{

	public function saving(Entity $model)
	{
		$attributes  = collect($model->getAttributes());
		$definitions = $model::getAttributeDefinitions();

		// Pipe definitions through saving method
		$definitions->each->saving($attributes, $model);
	}

	function saved(Entity $model)
	{
		$definitions = $model::getAttributeDefinitions();

		// Pipe definitions through saved method
		$definitions->each->saved($model);
	}

	function deleting(Entity $model)
	{
		$definitions = $model::getAttributeDefinitions();
		$definitions->each->deleting($model);
	}

}
