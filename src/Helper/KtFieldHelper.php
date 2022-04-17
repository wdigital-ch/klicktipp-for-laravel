<?php
/*
 * Copyright (c) - WDigital - 2022.
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

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

		foreach ($fields as $fieldKey => $fieldValue) {
			$newFieldArray[] = ucfirst(explode('field', $fieldKey)[1]);
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
		return $addKeyToString . ucfirst($field);
	}
}