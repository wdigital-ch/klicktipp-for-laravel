<?php

namespace WDigital\KlickTippForLaravel\Services;

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
			return KtResponsesHelper::getResponsesError($ktTagResponse);
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
			return KtResponsesHelper::getResponsesError($ktTagResponse);
		}
	}

	/**
	 * @param string $tagName
	 *
	 * @return mixed
	 */
	public function createTag(string $tagName): mixed
	{
		$ktTagResponse = $this->httpClient->post('tag', $tagName);

		if ($ktTagResponse->status() === 200) {
			return $ktTagResponse->json();
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse);
		}
	}

	/**
	 * @param string $name
	 * @param int    $tagId
	 *
	 * @return mixed
	 */
	public function updateTag(string $name, int $tagId): mixed
	{
		$ktTagResponse = $this->httpClient->put('tag/' . $tagId, $name);

		if ($ktTagResponse->status() === 200) {
			return $ktTagResponse->json();
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse);
		}
	}

	/**
	 * @param $tagId
	 *
	 * @return mixed
	 */
	public function deleteTag($tagId): mixed
	{
		$ktTagResponse = $this->httpClient->delete('tag/' . $tagId);

		if ($ktTagResponse->status() === 200) {
			return $ktTagResponse->json();
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse);
		}
	}


}