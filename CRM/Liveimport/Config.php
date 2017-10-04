<?php
/**
 * Maps all the configuration names on the technical keys
 * Uses singleton pattern to prevent to reduce database turnarounds
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 18-9-17 20:17
 * @license AGPL-3.0
 *
 */
class CRM_Liveimport_Config {

  private static $_singleton;

  private $_waarschuwenIgvNoodCustomFieldId;
  private $_telefoonIgvNoodCustomFieldId;
  private $_bijzonderhedenCustomFieldId;
  private $_verzekeringsNummerCustomFieldId;
  private $_participantRoleId;
  private $_teamRolCustomFieldId;
  private $_teamMemberOfTeamCustomFieldId;
  private $_teamDataCustomGroupTableName;
  private $_teamNrCustomFieldColumnName;
  /**
   * @return mixed
   */
  public function getTeamMemberOfTeamCustomFieldId() {
    return $this->_teamMemberOfTeamCustomFieldId;
  }

  /**
   * @return mixed
   */
  public function getTeamRolCustomFieldId() {
    return $this->_teamRolCustomFieldId;
  }

  /**
   * @return mixed
   */
  public function getParticipantRoleId() {
    return $this->_participantRoleId;
  }

  /**
   * @return mixed
   */
  public function getBijzonderhedenCustomFieldId() {
    return $this->_bijzonderhedenCustomFieldId;
  }
  /**
   * @return mixed
   */
  public function getVerzekeringsNummerCustomFieldId() {
    return $this->_verzekeringsNummerCustomFieldId;
  }

  public function getWaarschuwenIgvNoodCustomFieldId(){
    return $this->_waarschuwenIgvNoodCustomFieldId;
  }

  public function getTelefoonIgvNoodCustomFieldId(){
    return $this->_telefoonIgvNoodCustomFieldId;
  }

  /**
   * Constructor method: finds the ids by the names
   *
   * @param string $context
   */
  function __construct($context) {
    try {
      $this->_waarschuwenIgvNoodCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "Waarschuwen_in_geval_van_nood",
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field Waarschuwen in geval van nood in'.__FILE__.' on line'.__LINE__);
    }
    try {
      $this->_telefoonIgvNoodCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "Telefoon_in_geval_van_nood",
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field Telefoon_in_geval_van_nood in'.__FILE__.' on line'.__LINE__);
    }
    try {
      $this->_bijzonderhedenCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "Bijzonderheden",
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field Bijzonderheden in'.__FILE__.' on line'.__LINE__);
    }
    try {
      $this->_verzekeringsNummerCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "Verzekeringsnummer",
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Verzekeringsnummer in'.__FILE__.' on line'.__LINE__);
    }
    try{

      $this->_teamRolCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "team_role",
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Team Role in'.__FILE__.' on line'.__LINE__);
    }
    try{

      $this->_participantRoleId = civicrm_api3('OptionValue', 'getvalue', array(
        'return' => "value",
        'name' => "team_member",
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Option Value  team memer in'.__FILE__.' on line'.__LINE__);
    }
    try {

      $this->_teamMemberOfTeamCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "team_member_of_team",
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find custom field team member of team'.__FILE__.' on line '.__LINE__);
    }
    try {

      $_teamDataCustomGroup = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'team_data'));


      $this->_teamDataCustomGroupTableName = $_teamDataCustomGroup['table_name'];

    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Group  team_data in'.__FILE__.' on line'.__LINE__);
    }
    try{
      $_teamNrCustomField = civicrm_api3('CustomField', 'getsingle', array('name' => 'team_nr', 'custom_group_id' => $this->_teamDataCustomGroupId));
      $this->_teamNrCustomFieldColumnName = $_teamNrCustomField['column_name'];
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  team_nr in'.__FILE__.' on line'.__LINE__);
    }
  }

  /**
   * @return mixed
   */
  public function getTeamDataCustomGroupTableName() {
    return $this->_teamDataCustomGroupTableName;
  }

  /**
   * @return mixed
   */
  public function getTeamNrCustomFieldColumnName() {
    return $this->_teamNrCustomFieldColumnName;
  }

  /**
   * Singleton method
   *
   * @param string $context to determine if triggered from install hook
   * @return CRM_Liveimport_Config
   * @access public
   * @static
   */
  public static function singleton($context = null) {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Liveimport_Config($context);
    }
    return self::$_singleton;
  }

}