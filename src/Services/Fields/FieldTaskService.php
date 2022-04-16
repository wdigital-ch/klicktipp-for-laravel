<?php

namespace WDigital\KlickTippForLaravel\Services\Fields;

use WDigital\KlickTippForLaravel\Helper\KtResponsesHelper;
use WDigital\KlickTippForLaravel\Services\KlickTippBaseService;

class FieldTaskService extends KlickTippBaseService
{
	/**
	 * @var FieldTaskService|null $instance
	 */
	private static ?FieldTaskService $instance = null;

	/**
	 * ContactTasksService constructor.
	 */
	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * @return FieldTaskService
	 */
	public static function getInstance(): FieldTaskService
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function fieldList()
	{
		$ktTagResponse = $this->httpClient->get('field.json');

		if ($ktTagResponse->status() === 200) {
			return $ktTagResponse->json();
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), $ktTagResponse);
		}
	}
}