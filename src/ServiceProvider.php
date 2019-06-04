<?php

namespace Stylemix\Base;

use Illuminate\Support\ServiceProvider as BaseProvider;
use Stylemix\Base\Attributes\Attachment;
use Stylemix\Base\Attributes\BaseAttribute;
use Stylemix\Base\Attributes\Boolean;
use Stylemix\Base\Attributes\Datetime;
use Stylemix\Base\Attributes\Enum;
use Stylemix\Base\Attributes\Id;
use Stylemix\Base\Attributes\Location;
use Stylemix\Base\Attributes\LongText;
use Stylemix\Base\Attributes\Number;
use Stylemix\Base\Attributes\Relation;
use Stylemix\Base\Attributes\Text;
use Stylemix\Base\Facades\EntityForm;
use Stylemix\Base\Fields\Base;

class ServiceProvider extends BaseProvider
{

    /**
     * Register IoC bindings.
     */
    public function register()
    {
        // Bind the Form builder as a singleton on the container.
        $this->app->singleton(FormBuilder::class);
    }

    /**
     * Boot the package.
     */
    public function boot()
    {
		EntityForm::register(Number::class, function (Number $attribute) {
			return $this->basicFieldOptions(
				\Stylemix\Base\Fields\NumberField::make($attribute->fillableName),
				$attribute
			);
		});

		EntityForm::register(Id::class, function () {
			return null;
		});

		EntityForm::register(Boolean::class, function (Boolean $attribute) {
			return $this->basicFieldOptions(
				\Stylemix\Base\Fields\CheckboxField::make($attribute->fillableName),
				$attribute
			);
		});

		EntityForm::register(Datetime::class, function (Datetime $attribute) {
			return $this->basicFieldOptions(
				\Stylemix\Base\Fields\DatetimeField::make($attribute->fillableName),
				$attribute
			);
		});

		EntityForm::register(Text::class, function (Text $attribute) {
			return $this->basicFieldOptions(
				\Stylemix\Base\Fields\TextField::make($attribute->fillableName),
				$attribute
			);
		});

		EntityForm::register(Enum::class, function (Enum $attribute) {
			return $this->basicFieldOptions(
				\Stylemix\Base\Fields\SelectField::make($attribute->fillableName)
					->options($attribute->getSelectOptions()),
				$attribute
			);
		});

		EntityForm::register(LongText::class, function (LongText $attribute) {
			if ($attribute->editor) {
				return $this->basicFieldOptions(
					\Stylemix\Base\Fields\EditorField::make($attribute->fillableName),
					$attribute
				);
			}

			return $this->basicFieldOptions(
				\Stylemix\Base\Fields\TextareaField::make($attribute->fillableName),
				$attribute
			);
		});

		EntityForm::register(Attachment::class, function (Attachment $attribute) {
			return $this->basicFieldOptions(
				\Stylemix\Base\ExtraFields\AttachmentField::make($attribute->fillableName)
					->mimeTypes($attribute->mimeTypes)
					->mediaTag($attribute->name),
				$attribute
			);
		});

		EntityForm::register(Relation::class, function (Relation $attribute) {
			return $this->basicFieldOptions(
				\Stylemix\Base\ExtraFields\RelationField::make($attribute->fillableName)
					->setQuery(function (Base $field) use ($attribute) {
						return $attribute->getQueryBuilder($field->getResource());
					})
					->otherKey($attribute->getOtherKey()),
				$attribute
			);
		});

		EntityForm::register(Location::class, function (Location $attribute) {
			return $this->basicFieldOptions(
				\Stylemix\Base\ExtraFields\LocationField::make($attribute->fillableName),
				$attribute
			);
		});

    }

	protected function basicFieldOptions(Base $field, BaseAttribute $attribute)
	{
		return $field
			->required($attribute->required)
			->multiple($attribute->multiple);
    }

    /**
     * Which IoC bindings the provider provides.
     *
     * @return array
     */
    public function provides()
    {
        return array(
			FormBuilder::class
        );
    }
}
