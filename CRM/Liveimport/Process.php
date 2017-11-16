<?php
/**
 * Process the live import
 *
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 20-9-17 17:21
 * @license AGPL-3.0
 *
 */
class CRM_Liveimport_Process {

  const
    STEP_SIZE = 10;

  public static function calcSteps(){

    $calcRows = CRM_Core_DAO::singleValueQuery('select count(1) from import_livefeed where processed = %1',array(
      '1' => array('N','String')
    ));
    return array(ceil($calcRows / CRM_Liveimport_Process::STEP_SIZE), CRM_Liveimport_Process::STEP_SIZE);
  }
	
	public static function calcFinishSteps() {
		$config = CRM_Liveimport_Config::singleton();
		$event_id = CRM_Liveimport_DBUtils::getCurrentRoparunEventId();
		// Cancel all registration of teammebers who are not in the import feed.
		$sql = "SELECT COUNT(*) 
				FROM civicrm_participant
				WHERE civicrm_participant.role_id = %1 AND civicrm_participant.status_id = %2 AND civicrm_participant.event_id = %3
				";
		$params[1] = array($config->getParticipantRoleId(), 'Integer');
		$params[2] = array($config->getRegisteredParticipantStatusTypeId(), 'Integer');
		$params[3] = array($event_id, 'Integer');
		$calcRows = CRM_Core_DAO::singleValueQuery($sql, $params);
		return array(ceil($calcRows / CRM_Liveimport_Process::STEP_SIZE), CRM_Liveimport_Process::STEP_SIZE);
	}


  public static function importContact($dao,&$errors){

    // ToDo = toevoegen email en telefoon

    $config = CRM_Liveimport_Config::singleton();
    $apiParams = [];
    if ($contact_id) {
      $apiParams['id'] = $contact_id;
    } else {
    	// Try to find contact based on name and birth data
    	if (!empty($dao->birth_date) && $dao->birth_date != '0001-01-01') {
    		$apiGetParams['birth_date'] = $dao->geboortedatum;
	    	CRM_Utils_Date::convertToDefaultDate($apiGetParams, 32, 'birth_date');
    	}
	    $apiGetParams['first_name'] = $dao->voornaam;
	    $apiGetParams['middle_name'] = $dao->tussenvoegsel;
	    $apiGetParams['last_name'] = CRM_Liveimport_DBUtils::formatName($dao->achternaam,$dao->meisjesnaamtussenvoegsel,$dao->meisjesnaam);
	    $apiGetParams['contact_type'] = 'Individual';
			try {
				$count = civicrm_api3('Contact', 'getcount', $apiGetParams);
				if ($count) {
					$apiGetParams['return'] = 'id';
					$apiParams['id'] = civicrm_api3('Contact', 'getvalue', $apiGetParams);
				}
			} catch (Exception $e) {
				// Do nothing contact not found.
			}
    }
		
		if (!empty($dao->birth_date) && $dao->birth_date != '0001-01-01') { 
    	$apiParams['birth_date'] = $dao->geboortedatum;
    	CRM_Utils_Date::convertToDefaultDate($apiParams, 32, 'birth_date');
		}
    $apiParams['first_name'] = $dao->voornaam;
    $apiParams['middle_name'] = $dao->tussenvoegsel;
    $apiParams['last_name'] = CRM_Liveimport_DBUtils::formatName($dao->achternaam,$dao->meisjesnaamtussenvoegsel,$dao->meisjesnaam);
    $apiParams['contact_type'] = 'Individual';
    $apiParams['contact_sub_type'] = 'Teamlid';
    $apiParams['source'] = 'Liveimport';
    $apiParams['gender'] = CRM_Liveimport_DBUtils::translateGender($dao->geslacht);

    // Is Iset hier wel zo'n goed idee (maakt het onmogelijk het veld leeg te maken)

    if (isset($dao->waarschuwen_igv_nood)) {
      $apiParams['custom_' . $config->getWaarschuwenIgvNoodCustomFieldId()] = $dao->waarschuwen_igv_nood;
    }
    if (isset($dao->telefoon_igv_nood)) {
      $apiParams['custom_' . $config->getTelefoonIgvNoodCustomFieldId()] = $dao->telefoon_igv_nood;
    }
    if (isset($dao->verzekeringsnummer)) {
      $apiParams['custom_' . $config->getVerzekeringsNummerCustomFieldId()] = $dao->verzekeringsnummer;
    }
    if (isset($dao->bijzonderheden)) {
      $apiParams['custom_' . $config->getBijzonderhedenCustomFieldId()] = $dao->bijzonderheden;
    }

    $result = civicrm_api3('contact', 'create', $apiParams);

    return $result['id'];
  }

  public static function importPhone($dao, $contact_id, &$errors) {

    if (!empty($dao->telefoon)) {
      $apiParams = array();
      $apiParams ['phone'] = $dao->telefoon;
      $apiParams ['contact_id'] = $contact_id;
      $phoneId = CRM_Liveimport_DBUtils::findPhone($contact_id);
      if (isset($phoneId)) {
        $apiParams['id'] = $phoneId;
      }
      try {
        $result = civicrm_api3('phone', 'create', $apiParams);
      } catch (CiviCRM_API3_Exception $ex) {
        $errors[] = $ex->getMessage();
      }
    }
  }

  public static function importEmail($dao, $contact_id, &$errors) {
    if (!empty($dao->email)) {

      $apiParams = array();
      $apiParams ['email'] = $dao->email;
      $apiParams ['contact_id'] = $contact_id;
      $emailId = CRM_Liveimport_DBUtils::findEmail($contact_id);
      if (isset($emailId)) {
        $apiParams['id'] = $emailId;
      }
      try {
        $result = civicrm_api3('email', 'create', $apiParams);
      } catch (CiviCRM_API3_Exception $ex) {
        $errors[] = $ex->getMessage();
      }
    }
  }

  public static function importAddress($dao,$contact_id,&$errors){


    $adrParams['city'] = $dao->plaats;
    $adrParams['street_address'] = $dao->straat . ' ' . $dao->huisnummer;
    $adrParams['postal_code'] = $dao->postcode;
    $adrParams['location_type_id'] = 1;
    $adrParams['contact_id'] = $contact_id;

    $iso_code = CRM_Liveimport_DBUtils::translateCountry($dao->land);
    if(isset($iso_code)) {
      $adrParams['country'] = $iso_code;
    } else {
      $errors[] = 'Onbekend land '.$dao->land;
    }

    try {
      $address = civicrm_api3('address', 'getsingle', [
        'contact_id' => $contact_id,
        'is_primary' => 1,
        'result' => 'id',
        'location_type_id' => 1
      ]);
      $adrParams['id'] = $address['id'];
    } catch (CiviCRM_API3_Exception $ex) {

    }
    civicrm_api3('address', 'create', $adrParams);

  }

  public static function importParticipant($dao,$contact_id,$participantId,&$errors){

    $config = CRM_Liveimport_Config::singleton();
    $event_id = CRM_Liveimport_DBUtils::getCurrentRoparunEventId();

    $partParams['contact_id'] = $contact_id;
    $partParams['event_id'] = $event_id;

    if ($participantId) {
      $partParams['id'] = $participantId;
    }

    $partParams['role_id'] = $config->getParticipantRoleId();
		$partParams['status_id'] = $config->getRegisteredParticipantStatusTypeId();
		$partParams['campaign_id'] = CRM_Liveimport_DBUtils::getRoparunCampaignId($event_id);
		$partParams['custom_' . $config->getTeammemberNrCustomFieldId()] = $dao->roparunid;
		$partParams['custom_' . $config->getShowOnWebsiteCustomFieldId()] = 1;
		$partParams['custom_' . $config->getShowOnDonationFormCustomFieldId()] = 1;
		if (strtolower($dao->tonenalsdeelnemer) != 'ja') {
			if (!CRM_Liveimport_DBUtils::countExistingDonationsForTeamMember($event_id, $contact_id)) {
				// Set show on website to false when there are no donations on this teammember. Otherwise we keep show on website.
				$partParams['custom_' . $config->getShowOnWebsiteCustomFieldId()] = 0; 		
			}
			$partParams['custom_' . $config->getShowOnDonationFormCustomFieldId()] = 0;	
		}

    if (isset($dao->functie)) {
      $partParams['custom_' . $config->getTeamRolCustomFieldId()] = $dao->functie;
    }

    $teamContactId = CRM_Liveimport_DBUtils::findTeamContactId($dao->team);

    if (isset($teamContactId)) {
      $partParams['custom_' . $config->getTeamMemberOfTeamCustomFieldId()] = $teamContactId;
    } else {
      $errors[] = "team ".$dao->team." not found";
    }

    try {
      civicrm_api3('participant', 'create', $partParams);
    } catch (CiviCRM_API3_Exception $ex){
      $errors[] = $ex->getMessage();
    }
  }

	private static function findParticipantIdBasedOnRoparunId($roparunid, &$errors) {
		
		$config = CRM_Liveimport_Config::singleton();
		$event_id = CRM_Liveimport_DBUtils::getCurrentRoparunEventId();
		
		try {
			return civicrm_api3('Participant', 'getvalue', array(
				'return' => 'id',
				'custom_' . $config->getTeammemberNrCustomFieldId() => $roparunid,
				'event_id' => $event_id,
				'role_id' => $config->getParticipantRoleId(),
			));
		} catch (Exception $ex) {
			return false;
		}
		return false;
	}

  private static function processRecord($dao){

    try {
      $errors = array();
			// Find existing contact based the roparunid. 
			// The roparunid is stored in a custom field at the partcipant record.
			$participant_id = CRM_Liveimport_Process::findParticipantIdBasedOnRoparunId($dao->roparunid, $errors);
			$contact_id = false;
			if ($participant_id) {
				$contact_id = civicrm_api3('Participant', 'getvalue', array('id' => $participant_id, 'return' => 'contact_id'));
			}
			
      $contact_id = CRM_Liveimport_Process::importContact($dao,$contact_id, $errors);
      CRM_Liveimport_Process::importAddress($dao, $contact_id,$errors);
      CRM_Liveimport_Process::importPhone($dao, $contact_id,$errors);
      CRM_Liveimport_Process::importEmail($dao, $contact_id,$errors);
      CRM_Liveimport_Process::importParticipant($dao, $contact_id, $participant_id, $errors);
      $roparunid = $dao->roparunid;
      if(empty($errors)){
         CRM_Core_DAO::executeQuery('UPDATE import_livefeed SET message=%2, processed=%3 where roparunid=%1', array(
           1 => array($roparunid,'Integer'),
           2 => array('Success','String'),
           3 => array('P','String'),
         ));
      } else {
        $errormessage = implode(',',$errors);
        $upd = CRM_Core_DAO::executeQuery('UPDATE import_livefeed set message=%2, processed=%3 where roparunid=%1', array(
          1 => array($roparunid,'Integer'),
          2 => array($errormessage,'String'),
          3 => array('F','String'),
        ));
      }

    } catch (Exception $ex){
      Civi::log()->info("Catch Exception");
      Civi::log()->info(print_r($ex));
    };


  }

	public static function processFinish(CRM_Queue_TaskContext $ctx){
		$config = CRM_Liveimport_Config::singleton();
		$event_id = CRM_Liveimport_DBUtils::getCurrentRoparunEventId();
		// Cancel all registration of teammembers who are not in the import feed.
		$sql = "SELECT civicrm_participant.id, civicrm_participant.contact_id 
				FROM civicrm_participant
				INNER JOIN {$config->getTeamMemberDataCustomGroupTableName()} team_member_data ON team_member_data.entity_id = civicrm_participant.id
				WHERE civicrm_participant.role_id = %1 AND civicrm_participant.status_id = %2 AND civicrm_participant.event_id = %3
				AND team_member_data.{$config->getTeammemberNrCustomFieldColumnName()} NOT IN (SELECT roparunid FROM import_livefeed WHERE processed in ('F','P'))
				LIMIT %4";
		$params[1] = array($config->getParticipantRoleId(), 'Integer');
		$params[2] = array($config->getRegisteredParticipantStatusTypeId(), 'Integer');
		$params[3] = array($event_id, 'Integer');
		$params[4] = array(CRM_Liveimport_Process::STEP_SIZE, 'Integer');
		
    $dao = CRM_Core_DAO::executeQuery($sql, $params);
    try {
      while ($dao->fetch()) {
        $participantParams['id'] = $dao->id;
				$participantParams['status_id'] = $config->getCancelledParticipantStatusTypeId();
				if (!CRM_Liveimport_DBUtils::countExistingDonationsForTeamMember($event_id, $dao->contact_id)) {
					// Set show on website to false when there are no donations on this teammember. Otherwise we keep show on website.
					$participantParams['custom_' . $config->getShowOnWebsiteCustomFieldId()] = 0; 		
				}
				$participantParams['custom_' . $config->getShowOnDonationFormCustomFieldId()] = 0;
				civicrm_api3('Participant', 'create', $participantParams);	
      }
    } catch (Exception $ex){
      Civi::log()->info($ex);
    }
    return TRUE;
  }

  public static function testProcess() {

    $dao = CRM_Core_DAO::executeQuery('SELECT * FROM import_livefeed WHERE processed = %1 LIMIT %2', [
      '1' => ['F', 'String'],
      '2' => [CRM_Liveimport_Process::STEP_SIZE, 'Integer']
    ]);
    while ($dao->fetch()) {
      CRM_Liveimport_Process::processRecord($dao);
    }
  }

  public static function process(CRM_Queue_TaskContext $ctx){

    $dao = CRM_Core_DAO::executeQuery('select * from import_livefeed where processed = %1 limit %2',array(
      '1' => array ('N','String'),
      '2' => array (CRM_Liveimport_Process::STEP_SIZE,'Integer')
    ));
    try {
      while ($dao->fetch()) {
        CRM_Liveimport_Process::processRecord($dao);
      }
    } catch (Exception $ex){
      Civi::log()->info($ex);
    }
    return TRUE;
  }

}