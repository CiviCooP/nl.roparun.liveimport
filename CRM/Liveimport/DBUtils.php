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

  public static function formatName($lastname,$wifenameInfix,$wifeName){
    $result = $lastname;
    if(isset($wifeName)){
      $result = $result.'-';
      if(isset($wifenameInfix)){
        $result = $result.$wifenameInfix.' ';
      }
      $result = $result.$wifeName;
    }
    return $result;
  }

}