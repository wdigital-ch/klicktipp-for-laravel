<?php
/*
 * Copyright (c) - WDigital - 2024.
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace WDigital\KlickTippForLaravel\Services\Subscriptions;

use WDigital\KlickTippForLaravel\Helper\KtFieldHelper;
use WDigital\KlickTippForLaravel\Helper\KtResponsesHelper;
use WDigital\KlickTippForLaravel\Services\Fields\FieldTaskService;
use WDigital\KlickTippForLaravel\Services\KlickTippBaseService;

class SubscribeTaskService extends KlickTippBaseService
{
	/**
	 * @var SubscribeTaskService|null $instance
	 */
	private static ?SubscribeTaskService $instance = null;

	/**
	 * ContactTasksService constructor.
	 */
	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * @return SubscribeTaskService
	 */
	public static function getInstance(): SubscribeTaskService
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * @param string      $email                 // E-Mail-Adresse des Empfängers
	 * @param int         $subscriptionProcessId // (optional) ID des Double-Opt-in-Prozesses.
	 * @param int         $tagId                 // (optional) ID des Tags, mit dem der Empfänger markiert werden soll.
	 * @param array       $fields                // (optional) zusätzliche Daten des Empfängers, zum Beispiel Name,
	 *                                           Affiliate-ID, Anschrift, Kundennummer etc. Das Array muss so aufgebaut
	 *                                           sein, wie es die Funktion field_index zurückgibt.
	 * @param string|null $smsNumber             // (optional) SMS-Mobilnummer des Empfängers.
	 *
	 * @return mixed
	 */
	public function subscribe(string $email, int $subscriptionProcessId = 0, int $tagId = 0, array $fields = [], array $optionalFields = [], string $smsNumber = null): mixed
	{
		// Überprüft ob die E-Mail-Addresse schon vorhanden ist.
		/*	if (isset($this->searchSubscriberByEmail($email)['errorStatus']) === true && $this->searchSubscriberByEmail($email)['errorStatus'] === 404) {
				dd($this->searchSubscriberByEmail($email));
				return $this->searchSubscriberByEmail($email);
			}*/

		if (isset($this->searchSubscriberByEmail($email)['data']['successStatus']) === true && $this->searchSubscriberByEmail($email)['data']['successStatus'] === 200) {
			return $this->updateSubscribe($email, $this->searchSubscriberByEmail($email)['contactCloudId'], $fields, $optionalFields);
		}

		$requestFieldArray            = [];

		$requestFieldArray['email']     = $email;
		$requestFieldArray['listid']    = $subscriptionProcessId;
		$requestFieldArray['tagid']     = $tagId;
		$requestFieldArray['smsnumber'] = ($smsNumber != null) ? $smsNumber : '';
		$requestFieldArray['fields']    = $fields;

		$ktTagResponse = $this->httpClient->post('subscriber', $requestFieldArray);

		if ($ktTagResponse->status() === 200) {
			return $ktTagResponse->json();
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), $ktTagResponse);
		}

	}

	/**
	 * @param string $emailAddress // E-Mail-Adresse des Empfängers
	 *
	 * @return array
	 */
	public function searchSubscriberByEmail(string $emailAddress): array
	{
		$requestArray = [
			'email' => $emailAddress,
		];

		$ktTagResponse = $this->httpClient->post('subscriber/search', $requestArray);

		if ($ktTagResponse->status() === 200) {
			return [
				'data'           => KtResponsesHelper::getResponsesSuccess($ktTagResponse->status(), 'Für diesen Empfänger (' . $emailAddress . ') gibt es schon ein Konto.'),
				'contactCloudId' => (int)$ktTagResponse->json()[0],
			];
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), 'Diesen Empfänger (' . $emailAddress . ') gibt es nicht.');
		}
	}

	/**
	 * @param string      $newEmail
	 * @param int         $contactCloudId
	 * @param array       $fields
	 * @param string|null $smsNumber
	 *
	 * @return array
	 */
	public function updateSubscribe(string $newEmail, int $contactCloudId, array $fields = [], string $smsNumber = null): array
	{
		$ktTagResponse = $this->httpClient->put('/subscriber/' . $contactCloudId, $fields);

		if ($ktTagResponse->status() === 200) {
			return [
				'data' => KtResponsesHelper::getResponsesSuccess($ktTagResponse->status(), 'Der Datensatz für den Empfänger (' . $newEmail . ') wurde erfolgreich aktualisiert.'),
			];
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), 'Diesen Empfänger (' . $newEmail . ') gibt es nicht.');
		}
	}
}