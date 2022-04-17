<?php
/*
 * Copyright (c) - WDigital - 2022.
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace WDigital\KlickTippForLaravel\Services\Tags;

use WDigital\KlickTippForLaravel\Services\KlickTippBaseService;
use WDigital\KlickTippForLaravel\Helper\KtResponsesHelper;

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

	/**
	 *
	 * @return array|mixed|string
	 */
	public function tagList(): mixed
	{
		$ktTagResponse = $this->httpClient->get('tag');

		if ($ktTagResponse->status() === 200) {
			return $ktTagResponse->json();
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), $ktTagResponse);
		}
	}

	/**
	 * @param int $tagId
	 *
	 * @return mixed
	 */
	public function searchTagById(int $tagId): mixed
	{
		$ktTagResponse = $this->httpClient->get('tag/' . $tagId . '.json');

		if ($ktTagResponse->status() === 200) {
			return $ktTagResponse->json();
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), $ktTagResponse);
		}
	}

	/**
	 * @param string      $tagName
	 * @param string|null $tagDescription
	 *
	 * @return mixed
	 */
	public function createTag(string $tagName, ?string $tagDescription = null): mixed
	{
		$requestData = [
			"name" => $tagName,
			"text" => $tagDescription,
		];

		$ktTagResponse = $this->httpClient->post('tag', $requestData)->json();

		if ($ktTagResponse->status() === 200) {
			return 'Tag mit der ID: ' . $ktTagResponse . 'wurde erfolgreich erstellt.';
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), $ktTagResponse);
		}
	}

	/**
	 * @param int         $tagId          // ID des manuellen Tags/SmartLinks
	 * @param string|null $tagName        // optional: Name des manuellen Tags/SmartLinks
	 * @param string|null $tagDescription // optional: zusätzliche Informationen
	 *
	 * @return mixed
	 */
	public function updateTag(int $tagId, ?string $tagName = null, ?string $tagDescription = null): mixed
	{
		$requestData = [
			"name" => $tagName,
			"text" => $tagDescription,
		];

		$ktTagResponse = $this->httpClient->put('tag/' . $tagId . '', $requestData);

		if ($ktTagResponse->status() === 200) {
			if (in_array(true, $ktTagResponse->json()) == true) {
				return KtResponsesHelper::getResponsesSuccess($ktTagResponse->status(), 'Tag mit der ID: ' . $tagId . ' wurde erfolgreich aktualisiert.');
			}

			return KtResponsesHelper::getResponsesSuccess($ktTagResponse->status(), $ktTagResponse->json());

		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), $ktTagResponse->json());
		}
	}

	/**
	 * @param int $tagId // ID des manuellen Tags / SmartLinks
	 *
	 * @return mixed
	 */
	public function deleteTag(int $tagId): mixed
	{
		$ktTagResponse = $this->httpClient->delete('tag/' . $tagId);

		if ($ktTagResponse->status() === 200) {
			KtResponsesHelper::getResponsesSuccess($ktTagResponse->status(), $ktTagResponse);
			return 'Tag mit der ID: ' . $tagId . 'wurde erfolgreich gelöscht.';
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), $ktTagResponse->reason());
		}
	}
}