<?php
App::uses('Component', 'Controller');
class BreadcrumbComponent extends Component
{
	static public $crumbs		= array();
	static public $visible		= true;

	/**
	 * Constructor
	 *
	 * @param ComponentCollection $collection A ComponentCollection for this component
	 * @param array $settings Array of settings.
	 */
	public function __construct(ComponentCollection $collection, $settings = array())
	{
		if ( isset($settings['crumbs']) )
		{
			self::$crumbs = $settings['crumbs'];
			unset($settings['crumbs']);
		}

		parent::__construct($collection, $settings);
	}

	/**
	 * Add method
	 */
	static public function add($name, $link = null) {
		self::$crumbs[] = array($name, $link);
	}

	/*
	 * @usage Return the breadcrumbs to the controller
	 * @return void
	 */
	static public function get() {
		return self::$crumbs;
	}
}
