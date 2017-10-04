<?php

class CRM_Liveimport_Page_UploadResult extends CRM_Core_Page {

  private function readFailed(){
    $dao = CRM_Core_DAO::executeQuery('select roparunid,message from import_livefeed where processed=%1 limit %2', array(
      '1' => array('F','String'),
      '2' => array(20,'Integer')
    ));
    $failed = array();
    while($dao->fetch()){
      $row = array();
      $row['roparunid'] = $dao->roparunid;
      $row['message'] = $dao->message;
      $failed[]=$row;
    }
    return $failed;


  }

  public function run() {

    CRM_Utils_System::setTitle(ts('UploadResult'));
    $failed=array();
    $row=array();
    $row['roparunid'] = '$dao->roparunid';
    $row['message'] = '$dao->message';
    $failed[]=$row;
    $failed = $this->readFailed();
    // Example: Assign a variable for use in a template
    $this->assign('failed', $failed);

    parent::run();
  }

}
