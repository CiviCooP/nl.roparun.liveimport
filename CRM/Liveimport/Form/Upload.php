<?php
/**
 * Form for the upload of the live import feed files
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 19-9-17 15:04
 * @license AGPL-3.0
 *
 */
class CRM_Liveimport_Form_Upload extends CRM_Core_Form {
  public function buildQuickForm() {

    $config = CRM_Core_Config::singleton();

    $uploadFileSize = CRM_Utils_Number::formatUnitSize($config->maxFileSize . 'm', TRUE);
    //Fetch uploadFileSize from php_ini when $config->maxFileSize is set to "no limit".
    if (empty($uploadFileSize)) {
      $uploadFileSize = CRM_Utils_Number::formatUnitSize(ini_get('upload_max_filesize'), TRUE);
    }
    $uploadSize = round(($uploadFileSize / (1024 * 1024)), 2);
    $this->assign('uploadSize', $uploadSize);
    $this->add('File', 'uploadFile', ts('Import Data File'), 'size=30 maxlength=255', TRUE);
    $this->setMaxFileSize($uploadFileSize);
    $this->addRule('uploadFile', ts('File size should be less than %1 MBytes (%2 bytes)', array(
      1 => $uploadSize,
      2 => $uploadFileSize,
    )), 'maxfilesize', $uploadFileSize);
   // $this->addRule('uploadFile', ts('Input file must be in CSV format'), 'utf8File');
   // $this->addRule('uploadFile', ts('A valid file must be uploaded.'), 'uploadedfile');

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  public function preProcess(){
    if(isset($this->_submitFiles['uploadFile'])){
      $uploadFile = $this->_submitFiles['uploadFile'];
      $dao = new CRM_Core_DAO();
      $dao->query("truncate table import_livefeed");
      CRM_Liveimport_CSV::readCSVtoTable($uploadFile['tmp_name']);
    }
  }

  public function postProcess() {

    $queue = CRM_Queue_Service::singleton()->create(array(
      'type' => 'Sql',
      'name' => 'nl.roparun.liveimport.process',
      'reset' => TRUE, //do not flush queue upon creation
    ));

    list($countSteps,$stepSize) = CRM_Liveimport_Process::calcSteps();

    for($i=0;$i<=$countSteps;$i++){

      $task = new CRM_Queue_Task(
        array(
          'CRM_Liveimport_Process',
          'process'
        ), //call back method
        array(), //parameters,
        "Processing ". $i*$stepSize ." rows"
      );

      $queue->createItem($task);
    }

    list($countSteps, $stepSize) = CRM_Liveimport_Process::calcFinishSteps();
    for ($i = 0; $i <= $countSteps; $i++) {
      $task = new CRM_Queue_Task([
        'CRM_Liveimport_Process',
        'processFinish',
      ], //call back method
        [], //parameters,
        "Process Cancellations  " . $i * $stepSize);

      $queue->createItem($task);
    }

    $url = CRM_Utils_System::url('civicrm/liveimport/uploadresult', 'reset=1');;
    $runner = new CRM_Queue_Runner(array(
      'title' => ts('Importing the livefeed records'), //title fo the queue
      'queue' => $queue, //the queue object
      'errorMode'=> CRM_Queue_Runner::ERROR_ABORT, //abort upon error and keep task in queue
      'onEnd' => array('CRM_Liveimport_Form_Upload', 'onEnd'), //method which is called as soon as the queue is finished
      'onEndUrl' => $url,
    ));
    $runner->runAllViaWeb(); // does not return

    parent::postProcess();
  }

  static function onEnd(CRM_Queue_TaskContext $ctx) {
    CRM_Core_Session::setStatus('Did a lot of updates', '', 'success');
  }
}
