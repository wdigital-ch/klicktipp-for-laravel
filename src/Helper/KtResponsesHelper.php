<?php
/*
 * Copyright (c) - WDigital - 2022.
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace WDigital\KlickTippForLaravel\Helper;

class KtResponsesHelper
{
	/**
	 * @param int    $errorStatus
	 * @param string $errorMessage
	 *
	 * @return array
	 */
	public static function getResponsesError(int $errorStatus, string $errorMessage): array
	{
		return [
			'errorStatus'  => $errorStatus,
			'errorMessage' => $errorMessage,
		];
	}

	/**
	 * @param int    $successStatus
	 * @param string $successMessage
	 *
	 * @return array
	 */
	public static function getResponsesSuccess(int $successStatus, string $successMessage): array
	{
		return [
			'successStatus'  => $successStatus,
			'successMessage' => $successMessage,
		];
	}

	/**
	 * @param $text
	 *
	 * @return array
	 */
	private static function rebuildStatusCodeText($status, $text): array
	{
		if (str_contains($text, ' : ')) {
			$explodeText = explode(' : ', $text);

			return [
				'codeStatusText' => $explodeText[0],
				'text'           => $explodeText[1],
			];
		}

		return [
			'codeStatusText' => $status,
			'text'           => $text,
		];
	}
}