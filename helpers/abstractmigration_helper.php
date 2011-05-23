<?php defined('BASEPATH') or die('No direct script access allowed.');

abstract class AbstractMigration {
    function __get ($var) {
        return get_instance()->$var;
    }

    function up () {
        throw new Exception('Not Implemented.');
    }

    function down () {
        throw new Exception('Not Implemented.');
    }

    function timestamps () {
        $this->fields['created_at'] = array('type' => 'DATETIME', 'null' => false);
        $this->fields['updated_at'] = array('type' => 'DATETIME', 'null' => false);
    }
}
