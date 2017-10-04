<?php

/**
 *
 * reads the CSV import file in the load table
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 20-9-17 14:27
 * @license AGPL-3.0
 *
 */
class CRM_Liveimport_CSV {

  const
    NUM_ROWS_TO_INSERT = 100;

  public static function readCSVtoTable($file, $fieldSeparator = ';') {

    $fields = [
      'team',
      'naam',
      'functie',
      'voornaam',
      'tussenvoegsel',
      'achternaam',
      'meisjesnaamtussenvoegsel',
      'meisjesnaam',
      'geslacht',
      'straat',
      'huisnummer',
      'postcode',
      'plaats',
      'land',
      'geboortedatum',
      'telefoon',
      'waarschuwen_igv_nood',
      'telefoon_igv_nood',
      'verzekeringsnummer',
      'bijzonderheden',
      'tonenalsdeelnemer',
      'email',
    ];

    $fd = fopen($file, 'r');
    fgetcsv($fd, 0, $fieldSeparator);

    $dao = new CRM_Core_DAO();

    $sql = NULL;
    $first = TRUE;
    $count = 0;
    while ($row = fgetcsv($fd, 0, $fieldSeparator)) {

      if (!$first) {
        $sql .= ', ';
      }
      else {
        $first = FALSE;
      }

      $row = array_map(function ($string) {
        return trim($string, chr(0xC2) . chr(0xA0));
      }, $row);
      $row = array_map(['CRM_Core_DAO', 'escapeString'], $row);
      $sql .= "('" . implode("', '", $row) . "')";
      $count++;

      if ($count >= self::NUM_ROWS_TO_INSERT && !empty($sql)) {
        $sql = "INSERT IGNORE INTO import_livefeed " . implode(',', $fields) . " VALUES $sql";
        $dao->query($sql);

        $sql = NULL;
        $first = TRUE;
        $count = 0;
      }
    }

    flose($fd);

  }

}