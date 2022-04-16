<?php

namespace WDigital\KlickTippForLaravel\Helper;

class KtFieldHelper
{

	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	public static function rebuildFieldsFromContactCloud(array $fields): array
	{
		$newFieldArray = [];

		foreach ($fields as $field) {
			$newFieldArray[] = ucfirst(explode('field', $field)[1]);
		}

		return $newFieldArray;
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	public static function rebuildField(string $field): string
	{
		$addKeyToString = 'field';
		return $addKeyToString . $field;
	}
}