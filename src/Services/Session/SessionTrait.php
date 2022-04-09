<?php
/*
 * Copyright (c) - WDigital - 2022.
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace Wdigital\KlicktippForLaravel\Services\Session;

use Illuminate\Support\Facades\Http;

trait SessionTrait
{
	/**
	 * @return array
	 */
	protected function authentication(): array
	{
		$httpHeaders = [
			'Accept'                => 'application/json',
			'Content-Type'          => 'application/json',
		];

		$authSession = Http::withOptions(
			[
				'base_uri' => config('api_base_url'),

			]
		)->withHeaders($httpHeaders)->post(
			'account/login',
			[
				'username'     => config('api_username'),
				'password' => config('api_password'),
			]);
		return [
			'sessionIdentifier' => $authSession->header('cookie'),
			'sessionStart'      => microtime(true),
		];
	}

}