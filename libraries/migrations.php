<?php defined('BASEPATH') or die('No direct script access allowed.');

class Migrations {
    static public $dir = null;
    public $ci;
    public $all;

    function __construct () {
        if (self::$dir === null) {
            self::$dir = FCPATH.APPPATH.'migrations';
        }
        $this->ci =& get_instance();

        $this->create_version_table_if_not_exists;

        $this->all = $this->all_migrations();

    }

    function __get ($item) {
        $method = "get_{$item}";
        if (! method_exists($this, $method)) {
            die('<pre>'.print_r(debug_backtrace(),true));
        }
        return $this->$method();
    }

    function __set($item, $value) {
        $method = "set_{$item}";
        $this->$method($value);
    }

    function all_migrations () {
        $glob = glob(self::$dir."/*.php");
        $retval = array();

        if ($glob) {
            $retval = array();
            foreach ($glob as $filename) {
                $match = preg_match('/^(\d{14})_([A-Za-z0-9_]+)\.php$/', basename($filename), $matches);
                if ($match) {
                    $retval []= new Migration($filename);
                }
            }
        } else {
        }

        array_unshift($retval, new EmptyMigration);
        return $retval;
    }

    function create_version_table_if_not_exists () {
        $this->ci->load->database();
        $this->ci->load->dbforge();
        if (! $this->ci->db->table_exists('schema_version')) {
            $this->ci->dbforge->add_key('version', true);
            $this->ci->dbforge->add_field(array(
                'version' => array('type' => 'varchar', 'constraint' => 255),
            ));
            $this->ci->dbforge->create_table('schema_version');
            $this->ci->db->insert('schema_version', array(
                'version' => 'empty',
            ));
        }
    }

    function get_all_migrations () { return $this->all_migrations(); }
    function get_create_version_table_if_not_exists () { return $this->create_version_table_if_not_exists(); }
    function get_is_database_empty () { return $this->is_database_empty(); }

    function get_latest_timestamp () {
        $migrations = $this->all_migrations;
        return array_pop($migrations)->timestamp;
    }

    function get_migration_by_timestamp ($timestamp) {
        foreach ($this->all_migrations as $migration) {
            if ($migration->timestamp === $timestamp) {
                return $migration;
            }
        }
        throw new Exception('Should never reach here with timestamp of '.$timestamp);
    }


    function get_version () {return $this->version(); }

    function is_database_empty () {
        return $this->version === 'empty';
    }

    function run_down ($migration) {
        $migration->down();
    }

    function run_up ($migration) {
        #die('<pre>'.var_dump($migration,true));
        $migration->up();
    }

    function set_version ($version) {
        $this->ci->db->update('schema_version', array(
            'version' => $version,
        ));
    }

    function timestamp_of_migration_after ($migration) {
        $migrations = $this->all_migrations;
        #die('<pre>'.var_dump($migrations,true));
        while ($migrations) {
            $this_migration = array_shift($migrations);
            if ($this_migration->timestamp === $migration->timestamp) {
                return array_shift($migrations)->timestamp;
            }
        }

        throw new Exception('No more migrations');
    }

    function timestamp_of_migration_before ($migration) {
        $migrations = array_reverse($this->all_migrations);

        while ($migrations) {
            $this_migration = array_shift($migrations);
            if ($this_migration->timestamp === $migration->timestamp) {
                return array_shift($migrations)->timestamp;
            }
        }

        throw new Exception ('No previous migrations');
    }

    function to_empty () {
        return $this->to_version('empty');
    }

    function to_latest () {
        return $this->to_version($this->latest_timestamp);
    }

    function to_version ($version_to_match) {
        ob_get_clean();

        if ($version_to_match === 'latest') {
            $version_to_match = $this->latest_timestamp;
        }
        if ($version_to_match !== 'empty') {
            $version_to_match = $version_to_match * 1;
        }

        if ($version_to_match < ($this->version * 1)) {
            $current = $this->get_migration_by_timestamp($this->version);
            while ($current->timestamp != $version_to_match) {
                $this->run_down($current);
                if ($current->is_empty) {
                    break;
                }
                $current = $current->previous;
            }
        } else if ($version_to_match > ($this->version  * 1)) {
            $current = $this->get_migration_by_timestamp($this->version);
            while ($current->timestamp <= $version_to_match) {
                if (! $current->is_empty) {
                    $this->run_up($current);
                }
                if ($current->is_latest) {
                    break;
                }
                $current = $current->next;
            }
        }
    }

    function version () {
        return $this->ci->db->get('schema_version')->row()->version;
    }
}
