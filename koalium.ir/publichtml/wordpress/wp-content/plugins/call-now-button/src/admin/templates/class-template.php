<?php

namespace cnb\admin\templates;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\button\CnbButton;

class TemplateMetadataField {
	/**
	 * name of the field ("actionValue",etc)
	 * @var string
	 */
	public $name;
	/**
	 * editable, readonly, hidden
	 *
	 * @var string
	 */
	public $type;

	/**
	 * shown as context to the field
	 *
	 * @var string
	 */
	public $description;

	/**
	 * shows as a line (question) to the field
	 *
	 * @var string
	 */
	public $line;

	/**
	 * Should the field be required
	 *
	 * @var boolean
	 */
	public $required;

	/**
	 * @param $name
	 * @param $type
	 * @param $description
	 * @param $line
	 * @param bool $required
	 */
	public function __construct( $name, $type, $description, $line, $required = false ) {
		$this->name        = $name;
		$this->type        = $type;
		$this->description = $description;
		$this->line        = $line;
		$this->required    = $required;
	}
}

class TemplateMetadata {
	/**
	 * ID (or: Field name / selector), should be unique across all buttons/actions
	 *
	 * @var string
	 */
	public $id;

	/**
	 * @var TemplateMetadataField[]
	 */
	public $fields;

	/**
	 * A header for the template metadata "group"
	 *
	 * @var string
	 */
	public $title;

	/**
	 * @param $id
	 * @param $fields
	 * @param $title
	 */
	public function __construct( $id, $fields, $title = null ) {
		$this->id          = $id;
		$this->fields      = $fields;
		$this->title       = $title;
	}
}

/**
 * TODO: Note somewhere if there are PRO options (and how to deal with them)
 */
class Template {
	/**
	 * @var string
	 */
	public $id;
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string[]
	 */
	public $categories;
	/**
	 * URL of an image
	 * @var string
	 */
	public $image;
	/**
	 * Alt text for the image
	 * @var string
	 */
	public $image_alt;
	/**
	 * @var string
	 */
	public $description;
	/**
	 * In case this template contains PRO elements, this explains what
	 * happens if the current domain is not a PRO domain.
	 *
	 * @var string
	 */
	public $proFeatures;
	/**
	 * @var CnbButton
	 */
	public $button;
	/**
	 * @var TemplateMetadata[]
	 */
	public $metadata;

	/**
	 * @param string $id
	 * @param string $name
	 * @param string[] $categories
	 * @param string $image
	 * @param string $image_alt
	 * @param string $description
	 * @param CnbButton $button
	 * @param TemplateMetadata[] $metadata
	 */
	public function __construct( $id, $name, $categories, $image, $image_alt, $description, $button, $metadata ) {
		$this->id          = $id;
		$this->name        = $name;
		$this->categories  = $categories;
		$this->image       = $image;
		$this->image_alt   = $image_alt;
		$this->description = $description;
		$this->button      = $button;
		$this->metadata    = $metadata;
	}
}
