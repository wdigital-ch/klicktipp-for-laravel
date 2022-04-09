<?php
/*
 * Copyright (c) - WDigital - 2022.
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace Wdigital\KlicktippForLaravel\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Wdigital\KlicktippForLaravel\Services\Session\SessionTrait;

class BaseService
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

		/*
		 * Checks if the current instance is in development.
		 * If yes, the demo mode is set
		 */
		if (config('app.env') === 'local'
			|| config('app.debug') === true
		) {
			$httpHeaders['X-Domainrobot-Demo'] = true;
		}

		if (!empty($this->sessionIdentifier)) {
			$httpHeaders['X-Domainrobot-SessionId'] = $this->sessionIdentifier;
		}

		$this->httpClient = Http::withOptions(
			[
				'base_uri' => config('api.base_url'),
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