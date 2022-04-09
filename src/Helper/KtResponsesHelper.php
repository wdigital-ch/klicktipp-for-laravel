<?php

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
		$rebuildErrorMessage = self::rebuildStatusCodeText($errorMessage);

		return [
			'errorStatus'     => $errorStatus,
			'errorStatusText' => $rebuildErrorMessage['codeStatusText'],
			'errorMessage'    => $rebuildErrorMessage['text'],
		];
	}

	/**
	 * @param int    $successStatus
	 * @param string $successMessage
	 *
	 * @return array
	 */
	public static function getResponsesSuccess(int $successStatus, string $successMessage)
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
	private static function rebuildStatusCodeText($text): array
	{
		$explodeText = explode(' : ', $text);

		return [
			'codeStatusText' => $explodeText[0],
			'text'           => $explodeText[1],
		];
	}
}