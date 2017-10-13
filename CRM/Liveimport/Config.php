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
	private $_participantRegisteredStatusId;
	private $_participantCancelledStatusId;
  private $_teamRolCustomFieldId;
	private $_teamMemberDataCustomGroupTableName;
	private $_teammemberNrCustomFieldId;
	private $_teammemberNrCustomFieldColumnName;
	private $_showOnWebsiteCustomFieldId;
	private $_showOnDonationFormCustomFieldId;
  private $_teamMemberOfTeamCustomFieldId;
  private $_teamDataCustomGroupTableName;
  private $_teamNrCustomFieldColumnName;
	private $_roparunEventCustomGroupTableName;
	private $_endDateDonationsCustomFieldColumnName;
	private $_donatedTowardsCustomGroupId;
	private $_donatedTowardsCustomGroupTableName;
	private $_towardsTeamMemberCustomFieldId;
	private $_towardsTeamMemberCustomFieldColumnName;
	private $_donatieFinancialTypeId;
	private $_completedContributionStatusId;
	private $_roparunEventTypeId;
	private $_teamDataCustomGroupId;
	
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
	 * Getter for the id of custom field teammember_nr.
	 * 
	 * @return int
	 */
	public function getTeammemberNrCustomFieldId() {
		return $this->_teammemberNrCustomFieldId;
	}
	
	/**
	 * Getter for the column name of custom field teammember_nr.
	 * 
	 * @return string
	 */
	public function getTeammemberNrCustomFieldColumnName() {
		return $this->_teammemberNrCustomFieldColumnName;
	}
	
	/**
	 * Getter for the table name of custom group team_member_data.
	 * 
	 * @return string
	 */
	public function getTeamMemberDataCustomGroupTableName() {
		return $this->_teamMemberDataCustomGroupTableName;
	}
	
	/**
	 * Getter for the id of custom field website.
	 * @return int
	 */
	public function getShowOnWebsiteCustomFieldId() {
		return $this->_showOnWebsiteCustomFieldId;
	}
	
	/**
	 * Getter for the id of custom field donations.
	 * @return int
	 */
	public function getShowOnDonationFormCustomFieldId() {
		return $this->_showOnDonationFormCustomFieldId;
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
	 * Getter for the custom group table name of the custom group 'roparun event'.
	 */
	public function getRoparunEventCustomGroupTableName() {
		return $this->_roparunEventCustomGroupTableName;
	}
	
	/**
	 * Getter for the custom field column name of the custom field end date donations.
	 */
	public function getEndDateDonationsCustomFieldColumnName() {
		return $this->_endDateDonationsCustomFieldColumnName;
	}
	
	/**
	 * Getter for custom group id of donated towards.
	 */
	public function getDonatedTowardsCustomGroupId() {
		return $this->_donatedTowardsCustomGroupId;
	}
	
	/**
	 * Getter for custom group table name of donated towards.
	 */
	public function getDonatedTowardsCustomGroupTableName() {
		return $this->_donatedTowardsCustomGroupTableName;
	}
	
	/**
	 * Getter for custom field id of towards team member.
	 */
	public function getTowardsTeamMemberCustomFieldId() {
		return $this->_towardsTeamMemberCustomFieldId;
	}
	
	/**
	 * Getter for custom field column name of towards team member.
	 */
	public function getTowardsTeamMemberCustomFieldColumnName() {
		return $this->_towardsTeamMemberCustomFieldColumnName;
	}
	
	/**
	 * Getter for completed contribution status id.
	 */
	public function getCompletedContributionStatusId() {
		return $this->_completedContributionStatusId;
	}
	
	/**
	 * Getter for donation financial type id.
	 */
	public function getDonatieFinancialTypeId() {
		return $this->_donatieFinancialTypeId;
	}
	
	/** 
	 * Getter for the Roparun event type id.
	 */
	public function getRoparunEventTypeId() {
		return $this->_roparunEventTypeId;
	}
	
	/**
	 * Getter for the id of the registered participant status type
	 */
	public function getRegisteredParticipantStatusTypeId() {
		return $this->_participantRegisteredStatusId;
	}
	
	/**
	 * Getter for the id of the cancelled participant status type
	 */
	public function getCancelledParticipantStatusTypeId() {
		return $this->_participantCancelledStatusId;
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

      $this->_teamMemberDataCustomGroupTableName = civicrm_api3('CustomGroup', 'getvalue', array(
        'return' => "table_name",
        'name' => 'team_member_data',
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Teammember_nr in'.__FILE__.' on line'.__LINE__);
    }
		try{

      $this->_teammemberNrCustomFieldColumnName = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "column_name",
        'name' => "teammember_nr",
        'custom_group_id' => 'team_member_data',
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Teammember_nr in'.__FILE__.' on line'.__LINE__);
    }
		try{

      $this->_teammemberNrCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "teammember_nr",
        'custom_group_id' => 'team_member_data',
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Teammember_nr in'.__FILE__.' on line'.__LINE__);
    }
		try{

      $this->_showOnWebsiteCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "website",
        'custom_group_id' => 'team_member_data',
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Show on Website in'.__FILE__.' on line'.__LINE__);
    }
		try{

      $this->_showOnDonationFormCustomFieldId = civicrm_api3('CustomField', 'getvalue', array(
        'return' => "id",
        'name' => "donations",
        'custom_group_id' => 'team_member_data',
      ));
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Show on Website in'.__FILE__.' on line'.__LINE__);
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
			$this->_teamDataCustomGroupId = $_teamDataCustomGroup['id'];
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
		
		try {
			$_roparunEventCustomGroup = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'roparun_event'));
			$this->_roparunEventCustomGroupTableName = $_roparunEventCustomGroup['table_name'];
			$this->_roparunEventCustomGroupId = $_roparunEventCustomGroup['id'];
		} catch (Exception $ex) {
			throw new Exception('Could not find custom group for roparun events');
		}
		try {
			$_roparunEndDateDonationsCustomField = civicrm_api3('CustomField', 'getsingle', array('name' => 'end_date_donations', 'custom_group_id' => $this->_roparunEventCustomGroupId));
			$this->_endDateDonationsCustomFieldColumnName = $_roparunEndDateDonationsCustomField['column_name'];
		} catch (Exception $ex) {
			throw new Exception('Could not find custom field End Date Donations');
		}

		try {
			$_donatedTowardsCustomGroup = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'donated_towards'));
			$this->_donatedTowardsCustomGroupId = $_donatedTowardsCustomGroup['id'];
			$this->_donatedTowardsCustomGroupTableName = $_donatedTowardsCustomGroup['table_name'];
		} catch (Exception $ex) {
			throw new Exception('Could not find custom group for Donated Towards');
		}
		try {
			$_towardsTeamMemberCustomField = civicrm_api3('CustomField', 'getsingle', array('name' => 'towards_team_member', 'custom_group_id' => $this->_donatedTowardsCustomGroupId));
			$this->_towardsTeamMemberCustomFieldColumnName = $_towardsTeamMemberCustomField['column_name'];
			$this->_towardsTeamMemberCustomFieldId = $_towardsTeamMemberCustomField['id'];
		} catch (Exception $ex) {
			throw new Exception('Could not find custom field Towards Team Member');
		}
		try {
			$this->_completedContributionStatusId = civicrm_api3('OptionValue', 'getvalue', array(
				'return' => 'value',
				'name' => 'Completed',
				'option_group_id' => 'contribution_status',
			));
		} catch (Exception $ex) {
			throw new Exception ('Could not retrieve the Contribution status completed');
		}
		try {
			$this->_donatieFinancialTypeId = civicrm_api3('FinancialType', 'getvalue', array(
				'name' => 'Donatie',
				'return' => 'id',
			));
		} catch (Exception $e) {
			throw new Exception('Could not retrieve financial type Donatie');
		}
		try {
			$this->_participantRegisteredStatusId = civicrm_api3('ParticipantStatusType', 'getvalue', array(
				'name' => 'Registered',
				'return' => 'id'
			));
		} catch (Exception $e) {
			throw new Exception ('Could not find registered participant status type');
		}
		try {
			$this->_participantCancelledStatusId = civicrm_api3('ParticipantStatusType', 'getvalue', array(
				'name' => 'Cancelled',
				'return' => 'id'
			));
		} catch (Exception $e) {
			throw new Exception ('Could not find cancelled participant status type');
		}
		try {
			$this->_roparunEventTypeId = civicrm_api3('OptionValue', 'getvalue', array(
				'return' => 'value',
				'name' => 'Roparun',
				'option_group_id' => 'event_type',
			));
		} catch (Exception $ex) {
			throw new Exception ('Could not retrieve the Roparun Event Type');
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