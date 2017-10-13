<?php
/**
 * Helper functions for the common database actions
 *
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 26-9-17 17:21
 * @license AGPL-3.0
 *
 */
class CRM_Liveimport_DBUtils {
	
	/**
	 * Returns the ID of the next roparun event.
	 * 
	 * @return int 
	 */
	public static function getCurrentRoparunEventId() {
		$config = CRM_Liveimport_Config::singleton();
		$id = CRM_Core_DAO::singleValueQuery("
			SELECT civicrm_event.id
			FROM civicrm_event
			INNER JOIN `{$config->getRoparunEventCustomGroupTableName()}` ON `{$config->getRoparunEventCustomGroupTableName()}`.entity_id = civicrm_event.id 
			WHERE 
				civicrm_event.event_type_id = '".$config->getRoparunEventTypeId()."'
				AND DATE(`{$config->getRoparunEventCustomGroupTableName()}`.`{$config->getEndDateDonationsCustomFieldColumnName()}`) > NOW()
		");
		if (!$id) {
			throw new Exception('Could not find an active Roparun Event');
		}
		return $id;
	}
	
	/**
	 * Returns the campaign ID of the roparun event.
	 * 
	 * @param int $event_id 
	 * 	ID of the event.
	 * @return int
	 */
	public static function getRoparunCampaignId($event_id) {
		$params[1] = array($event_id, 'Integer');
		$campaign_id = CRM_Core_DAO::singleValueQuery("SELECT campaign_id FROM civicrm_event WHERE id = %1", $params);
		return $campaign_id;  
	}
	
	public static function countExistingDonationsForTeamMember($event_id, $contact_id) {
		$config = CRM_Liveimport_Config::singleton();
		
		$financialTypeIds[] = $config->getDonatieFinancialTypeId();
		
		$sql = "SELECT COUNT(*) 
			FROM civicrm_contribution
			INNER JOIN civicrm_event ON civicrm_event.campaign_id = civicrm_contribution.campaign_id
			INNER JOIN `{$config->getDonatedTowardsCustomGroupTableName()}` donated_towards ON donated_towards.entity_id = civicrm_contribution.id
			WHERE donated_towards.`{$config->getTowardsTeamMemberCustomFieldColumnName()}` = %1
				AND civicrm_event.id = %2
				AND civicrm_contribution.is_test = 0
				AND civicrm_contribution.financial_type_id IN (" . implode(",", $financialTypeIds) . ")
				AND civicrm_contribution.contribution_status_id = %3";
		$params[1] = array($contact_id, 'Integer');
		$params[2] = array($event_id, 'Integer');
		$params[3] = array($config->getCompletedContributionStatusId(), 'Integer');
		return CRM_Core_DAO::singleValueQuery($sql, $params);
	}

   public static function findExternalIdentifier($identifier){
     return CRM_Core_DAO::singleValueQuery("select id from civicrm_contact where external_identifier = %1",array(
       '1' => array($identifier,'Integer')
     ));
   }

   public static function findParticipant($event_id, $contact_id){
     return CRM_Core_DAO::singleValueQuery("select id from civicrm_participant where event_id = %1 and contact_id=%2",array(
       '1' => array($event_id,'Integer'),
       '2' => array($contact_id,'Integer')
     ));
   }

   public static function findPhone($contact_id){
     return  CRM_Core_DAO::singleValueQuery("select id from civicrm_phone where contact_id=%1 and is_primary=1",array(
       '1' => array($contact_id,'Integer')
     ));
   }

   public static function findEmail($contact_id){
    return  CRM_Core_DAO::singleValueQuery("select id from civicrm_email where contact_id=%1 and is_primary=1",array(
      '1' => array($contact_id,'Integer')
    ));
   }

   public static function findTeamContactId($teamNr){
    $config = CRM_Liveimport_Config::singleton();
    $sql = "select part.contact_id from civicrm_participant part
            join {$config->getTeamDataCustomGroupTableName()} cd ON (cd.entity_id = part.id )
            where {$config->getTeamNrCustomFieldColumnName()}=%1";

    return  CRM_Core_DAO::singleValueQuery($sql,array(
      '1' => array($teamNr,'Integer')
    ));

    
    
   }

   public static function translateGender($gender){
      switch($gender){
        case "Vrouwelijk" : return 1;
        case "Mannelijk" : return  2;
        return 3;
      }
   }

  /**
   * Translate the names used in the Live Import site to the
   * iso counry code's.
   *
   * @param $name
   *
   * @return String
   */
  public static function translateCountry($name){
    $countries =
      array('Duitsland'=>'DE',
        'België' => 'BE',
      'Engeland' => 'GB',
      'Noorwegen' => 'NO',
      'Albanië' => 'AL',
      'Frankrijk'=> 'FR',
      'Zwitserland' => 'CH',
      'Schotland' => 'GB',
      'Nederland' => 'NL',
      );

    if(!isset($name)){
      return NULL;
    } elseif(array_key_exists($name,$countries)) {
      return $countries[$name];
    } else {
      return NULL;
    }
  }

  public static function formatName($lastname,$maidennameInfix,$maidenName){
    $result = $lastname;
    if(!empty($maidenName)){
      $result = $result.'-';
      if(!empty($maidennameInfix)){
        $result = $result.$wifenameInfix.' ';
      }
      $result = $result.$maidenName;
    }
    return $result;
  }

}