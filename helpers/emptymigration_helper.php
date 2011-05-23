<?php defined('BASEPATH') or die('No direct script access allowed.');

class EmptyMigration extends Migration {
    function __construct () {
        $this->timestamp = 'empty';
        $this->name = 'Empty Database';
    }

    function is_empty () {
        return true;
    }

    function previous () {
        throw new Exception('Cannot get previous of empty');
    }
}
