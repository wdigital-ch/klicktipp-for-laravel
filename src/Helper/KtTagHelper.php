<?php

namespace WDigital\KlickTippForLaravel\Helper;

class KtTagHelper
{

	/**
	 * @param array  $tagList
	 * @param string $tagName
	 *
	 * @return bool|array
	 */
	public function searchTagByName(array $tagList, string $tagName): bool|array
	{
		foreach ($tagList as $tagKey => $tagValue) {
			if ($tagValue == $tagName) {
				return [
					'id'   => $tagKey,
					'name' => $tagValue,
				];
			}
		}

		return false;
	}
}