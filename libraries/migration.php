<?php defined('BASEPATH') or die('No direct script access allowed.');

class Migration {
  function __construct ($config = array()) {
    $this->dir = FCPATH.APPPATH.'migrations';

  }

  function is_database_empty () {
    return false;
  }

  function all_filenames () {
    $glob = glob("{$this->dir}/*.php");
    if (! $glob) {
      return array();
    }

    $retval = array();
    foreach ($glob as $filename) {
      $match = preg_match('/^(\d{14})_([A-Za-z0-9_]+)\.php$/', basename($filename), $matches);
      if ($match) {
        $retval []= $filename;
      }
    }
    return $retval;
  }

  function info ($filename) {
    preg_match('/^(\d{14})_([A-Za-z0-9_]+)\.php$/', basename($filename), $matches);

    $retval['timestamp'] = $matches[1];
    $retval['name'] = $matches[2];

    $retval['formatted_timestamp'] = DateTime::createFromFormat(
      'YmdHis',
      $retval['timestamp'],
      new DateTimeZone('UTC')
    )->format('l, F j, Y \a\t H:i:s');

    $retval['current'] = $this->current_version() === $retval['timestamp'];
    $retval['content'] = file_get_contents($filename);

    return $retval;
  }



  function migrate_to ($version) {
  }

  function last_version () {
  }

  function current_version () {
  }

  function is_last_version ($version) {
    return $this->last_version() === $version;
  }

  function create ($name, $time = 'now') {
    $date = new DateTime($time, new DateTimeZone('UTC'));
    $date = $date->format('Ymdhis');

    $name = strtr($name, array(
      ' ' => '_',
    ));

    $filename = FCPATH.APPPATH."migrations/{$date}_{$name}.php";
    $classname = "Migration_{$date}_{$name}";

    $migration = strtr(file_get_contents(dirname(__FILE__).'/../views/migration_template.php'), array(
      'CLASS_NAME_HERE' => $classname,
    ));
    file_put_contents($filename, $migration);
    chmod($filename, 0777);
  }
}
