<?php
/*
 * Copyright (c) - WDigital - 2022.
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

		// Füge Daten zu Felder aus der KlickTipp ContactCloud.
		$fieldTaskServiceInstance     = FieldTaskService::getInstance();
		$getFieldListFromContactCloud = $fieldTaskServiceInstance->fieldList();

		$rebuildFieldFromContactCloud = KtFieldHelper::rebuildFieldsFromContactCloud($getFieldListFromContactCloud);
		$requestFieldArray            = [];

		// Entfernt die Underline im Key und schreibt den ersten Buchstaben nach dem Underline gross.
		foreach ($fields as $fieldKey => $fieldValue) {
			//dd(strpos($fieldKey, '_') !== false);
			if (str_contains($fieldKey, '_')) {
				$explodeUnderlineFromInputFields = explode('_', $fieldKey);
				$keyBeforeUnderline              = $explodeUnderlineFromInputFields[0];
				$keyAfterUnderline               = $explodeUnderlineFromInputFields[1];
				$keyAfterRecomposed              = $keyBeforeUnderline . ucfirst($keyAfterUnderline);
			} else {
				$keyAfterRecomposed = $fieldKey;
			}

			foreach ($rebuildFieldFromContactCloud as $fieldFromContactCloudKey => $fieldFromContactCloudValue) {
				$inputFieldRebuildToContactCloudStringFormat = KtFieldHelper::rebuildField($keyAfterRecomposed);

				// Überprüft ob der ContactCloudKey mit dem gerade übergebenen Feld zusammenpasst und setzt den Wert zum jeweiligen Feld.
				if ($fieldFromContactCloudValue === ucfirst($keyAfterRecomposed)) {
					$requestFieldArray['fields'][$inputFieldRebuildToContactCloudStringFormat] = $fieldValue;
				}

				if (!empty($optionalFields)) {
					foreach ($getFieldListFromContactCloud as $contactCloudFieldKey => $contactCloudFieldValue) {
						foreach ($optionalFields as $optionalFieldNameKey => $optionalFieldNameValue) {
							if ($contactCloudFieldValue === $optionalFieldNameKey) {
								$requestFieldArray['fields'][$contactCloudFieldKey] = $optionalFieldNameValue;
							}
						}
					}
				}
			}
		}

		$requestFieldArray['email']     = $email;
		$requestFieldArray['listid']    = $subscriptionProcessId;
		$requestFieldArray['tagid']     = $tagId;
		$requestFieldArray['smsnumber'] = ($smsNumber != null) ? $smsNumber : '';

		$ktTagResponse = $this->httpClient->post('subscriber.json', $requestFieldArray);

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
	 * @param array       $optionalFields
	 * @param string|null $smsNumber
	 *
	 * @return array
	 */
	public function updateSubscribe(string $newEmail, int $contactCloudId, array $fields = [], array $optionalFields = [], string $smsNumber = null): array
	{
		// Füge Daten zu Felder aus der KlickTipp ContactCloud.
		$fieldTaskServiceInstance     = FieldTaskService::getInstance();
		$getFieldListFromContactCloud = $fieldTaskServiceInstance->fieldList();

		$rebuildFieldFromContactCloud = KtFieldHelper::rebuildFieldsFromContactCloud($getFieldListFromContactCloud);
		$requestFieldArray            = [];

		// Entfernt die Underline im Key und schreibt den ersten Buchstaben nach dem Underline gross.
		foreach ($fields as $fieldKey => $fieldValue) {

			if (str_contains($fieldKey, '_')) {
				$explodeUnderlineFromInputFields = explode('_', $fieldKey);
				$keyBeforeUnderline              = $explodeUnderlineFromInputFields[0];
				$keyAfterUnderline               = $explodeUnderlineFromInputFields[1];
				$keyAfterRecomposed              = $keyBeforeUnderline . ucfirst($keyAfterUnderline);
			} else {
				$keyAfterRecomposed = $fieldKey;
			}

			foreach ($rebuildFieldFromContactCloud as $fieldFromContactCloudKey => $fieldFromContactCloudValue) {
				$inputFieldRebuildToContactCloudStringFormat = KtFieldHelper::rebuildField($keyAfterRecomposed);

				// Überprüft ob der ContactCloudKey mit dem gerade übergebenen Feld zusammenpasst und setzt den Wert zum jeweiligen Feld.
				if ($fieldFromContactCloudValue === ucfirst($keyAfterRecomposed)) {
					$requestFieldArray['fields'][$inputFieldRebuildToContactCloudStringFormat] = $fieldValue;
				}

				if (!empty($optionalFields)) {
					foreach ($getFieldListFromContactCloud as $contactCloudFieldKey => $contactCloudFieldValue) {
						foreach ($optionalFields as $optionalFieldNameKey => $optionalFieldNameValue) {
							if ($contactCloudFieldValue === $optionalFieldNameKey) {
								$requestFieldArray['fields'][$contactCloudFieldKey] = $optionalFieldNameValue;
							}
						}
					}
				}
			}
		}

		$ktTagResponse = $this->httpClient->put('/subscriber/' . $contactCloudId, $requestFieldArray);

		if ($ktTagResponse->status() === 200) {
			return [
				'data' => KtResponsesHelper::getResponsesSuccess($ktTagResponse->status(), 'Der Datensatz für den Empfänger (' . $newEmail . ') wurde erfolgreich aktualisiert.'),
			];
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), 'Diesen Empfänger (' . $newEmail . ') gibt es nicht.');
		}
	}
}