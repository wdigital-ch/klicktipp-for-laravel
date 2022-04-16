<?php

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
	public function subscribe(string $email, int $subscriptionProcessId = 0, int $tagId = 0, array $fields = [], string $smsNumber = null): mixed
	{
		// Füge Daten zu Felder aus der KlickTipp ContactCloud
		$fieldTaskServiceInstance     = FieldTaskService::getInstance();
		$getFieldListFromContactCloud = $fieldTaskServiceInstance->fieldList();
		//dd($getFieldListFromContactCloud);
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

				//	dd($keyAfterRecomposed);
			} else {
				$keyAfterRecomposed = $fieldKey;
			}

			foreach ($rebuildFieldFromContactCloud as $fieldFromContactCloudKey => $fieldFromContactCloudValue) {
				$inputFieldRebuildToContactCloudStringFormat = KtFieldHelper::rebuildField($keyAfterRecomposed);

				// Überprüft ob der ContactCloudKey mit dem gerade übergebenen Feld zusammenpasst und setzt den Wert zum jeweiligen Feld.
				if ($fieldFromContactCloudValue === ucfirst($keyAfterRecomposed)) {
					$requestFieldArray['fields'][$inputFieldRebuildToContactCloudStringFormat] = $fieldValue;
				}

				foreach ($getFieldListFromContactCloud as $contactCloudFieldKey => $contactCloudFieldValue) {
					if ($contactCloudFieldValue === 'Affiliate ID') {
						$requestFieldArray['fields'][$contactCloudFieldKey] = '12345567';
					}
				}
			}
		}

		$requestFieldArray['email']     = $email;
		$requestFieldArray['listid']    = $subscriptionProcessId;
		$requestFieldArray['tagid']     = $tagId;
		$requestFieldArray['smsnumber'] = ($smsNumber != null) ? $smsNumber : '';

		$ktTagResponse = $this->httpClient->post('subscriber', $requestFieldArray);

		if ($ktTagResponse->status() === 200) {
			return $ktTagResponse->json();
		} else {
			return KtResponsesHelper::getResponsesError($ktTagResponse->status(), $ktTagResponse);
		}
	}
}