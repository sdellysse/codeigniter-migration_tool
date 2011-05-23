<?php defined('BASEPATH') or die('No direct script access allowed.');

if (! class_exists('Migration')) {
    class Migration {
        static function is_migration_filename ($filename) {
            return 0 !== preg_match('/^(\d{14})_([A-Za-z0-9_]+)\.php$/', basename($filename));
        }

        static function create ($name, $time = 'now') {
            $timestamp = new DateTime($time, new DateTimeZone('UTC'));
            $timestamp = $timestamp->format('Ymdhis');

            $retval = new Migration('creating new');
            $retval->timestamp = $timestamp;
            $retval->name = strtr($name, array(' ' => '_'));

            $retval->content = get_instance()->load->view(
                Migration_Controller::$view_dir.'/migration_template.php.php',
                array('class_name' => $retval->classname),
                true
            );

            file_put_contents($retval->filename, $retval->content);
            chmod($retval->filename, 0777);

            return new Migration($retval->filename);
        }

        private $_data;

        function __get ($item) {
            $method = "get_$item";
            if (method_exists($this, $method)) {
                return $this->$method();
            }
            return $this->_data[$item];
        }

        function __set ($item, $value) {
            $method = "set_{$item}";
            if (method_exists($this, $method)) {
                $this->$method($value);
                return;
            }
            $this->_data[$item] = $value;
        }

        function __construct ($filename) {
            if ($filename !== 'creating new') {
                $this->filename = $filename;
                preg_match('/^(\d{14})_([A-Za-z0-9_]+)\.php$/', basename($filename), $matches);

                $this->timestamp = $matches[1];
                $this->name = $matches[2];
            }
        }

        function down () {
            get_instance()->db->trans_start();
                if (! $this->is_empty) {
                    require $this->filename;
                    $instance = new $this->classname;
                    $instance->down();
                }
                get_instance()->migrations->version = $this->previous->timestamp;
            get_instance()->db->trans_complete();
        }

        function get_classname () {
            return "Migration_{$this->timestamp}_{$this->name}";
        }

        function get_content () {
            if (isset($this->_data['content'])) {
                return $this->_data['content'];
            } else {
                $this->_data['content'] = file_get_contents($this->filename);
                return $this->get_content();
            }
        }

        function get_filename () {
            return Migrations::$dir."/{$this->timestamp}_{$this->name}.php";
        }

        function get_formatted_timestamp () {
            return DateTime::createFromFormat(
                'YmdHis',
                $this->timestamp,
                new DateTimeZone('UTC')
            )->format('l, F j, Y \a\t H:i:s');
        }

        function get_is_empty () {
            return $this->is_empty();
        }

        function get_is_latest () {
            return $this->is_latest();
        }

        function get_next () {
            return $this->next();
        }

        function get_previous () {
            return $this->previous();
        }

        function get_is_current () {
            return get_instance()->migrations->version === $this->timestamp;
        }

        function is_empty () {
            return false;
        }

        function is_latest () {
            $migrations = get_instance()->migrations->all_migrations;
            return $this->timestamp === array_pop($migrations)->timestamp;
        }

        function previous () {
            $previous_timestamp = get_instance()->migrations->timestamp_of_migration_before($this);
            $previous = get_instance()->migrations->get_migration_by_timestamp($previous_timestamp);
            return $previous;
        }

        function next () {
            $next_timestamp = get_instance()->migrations->timestamp_of_migration_after($this);
            #die('<pre>'.var_dump($next_timestamp,true));
            $next = get_instance()->migrations->get_migration_by_timestamp($next_timestamp);
            return $next;
        }

        function up () {
            require $this->filename;
            $instance = new $this->classname;
            $instance->up();
            #die('<pre>'.var_dump($instance, true));
            get_instance()->migrations->version = $this->timestamp;
        }
    }
}
