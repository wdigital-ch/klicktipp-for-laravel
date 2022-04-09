<?php
/*
 * Copyright (c) - WDigital - 2022.
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace WDigital\KlickTippForLaravel\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use WDigital\KlickTippForLaravel\Services\Session\SessionTrait;

class KlickTippBaseService
{
	use SessionTrait;

	/**
	 * @var int $sessionStart Timestamp at which the session identifier was generated
	 */
	protected int $sessionStart = 0;

	/**
	 * @var string $sessionIdentifier Value of the session identifier
	 */
	protected string $sessionIdentifier = '';

	/**
	 * @var PendingRequest $httpClient Laravel http client instance
	 */
	protected PendingRequest $httpClient;

	/**
	 * BaseService constructor.
	 */
	protected function __construct()
	{
		$this->callAuthentication();
	}

	protected function callAuthentication()
	{
		$session                 = $this->authentication();
		$this->sessionIdentifier = $session['sessionIdentifier'];
		$this->sessionStart      = $session['sessionStart'];

		$this->initializeHttpClient();
	}

	/**
	 * Initializes a new http client instance
	 *
	 * @return void
	 */
	public function initializeHttpClient()
	{
		$httpHeaders = [
			'Accept'                => 'application/json',
			'Content-Type'          => 'application/json',
		];

		$this->httpClient = Http::withOptions(
			[
				'base_uri' => config('klicktipp-for-laravel.api_base_url'),
			]
		)
			->withHeaders($httpHeaders);

		/*
		 * Checks if the current instance is in live mode.
		 * If no, the verification check for https is overridden
		 */
		if (config('app.env') !== 'production') {
			$this->httpClient->withoutVerifying();
		}
	}

	/**
	 * Checks the current session identifier to make sure it is not older than 3600 seconds
	 *
	 * @return bool
	 */
	public function checkSession(): bool
	{
		$sessionResult = false;
		$currentTime   = microtime(true);

		if ($currentTime - $this->sessionStart <= 3600) {
			$sessionResult = true;
		}

		return $sessionResult;
	}

}