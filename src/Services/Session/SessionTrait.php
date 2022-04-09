<?php
/*
 * Copyright (c) - WDigital - 2022.
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace WDigital\KlickTippForLaravel\Services\Session;

use Illuminate\Support\Facades\Http;

trait SessionTrait
{
	/**
	 * @return array
	 */
	protected function authentication(): array
	{
		$httpHeaders = [
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		];

		$authSession = Http::withHeaders($httpHeaders)->post(
			config('klicktipp.api_base_url') . 'account/login.json',
			[
				'username' => config('klicktipp.api_username'),
				'password' => config('klicktipp.api_password'),
			]);

		if ($authSession->failed() === false) {
			return [
				'sessionIdentifier' => $authSession->json()['sessid'],
				'sessionName'       => $authSession->json()['session_name'],
				'sessionStart'      => microtime(true),
			];
		} else {
			return [
				'errorMessage' => 0,
			];
		}
	}
}