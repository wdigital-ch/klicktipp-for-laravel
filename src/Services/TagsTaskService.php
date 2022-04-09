<?php

namespace WDigital\KlickTippForLaravel\Services;

use WDigital\KlickTippForLaravel\Services\KlickTippBaseService;

/**
 * Class TagsTaskService
 *
 * @package WDigital\KlickTippForLaravel\Services
 */

class TagsTaskService extends KlickTippBaseService
{
	/**
	 * @var TagsTaskService|null $instance
	 */
	private static ?TagsTaskService $instance = null;

	/**
	 * ContactTasksService constructor.
	 */
	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * @return TagsTaskService
	 */
	public static function getInstance(): TagsTaskService
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function tagList()
	{
		return $this->httpClient->get('tag')->json();
	}
}