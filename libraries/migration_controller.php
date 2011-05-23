<?php defined('BASEPATH') or die('No direct script access allowed');

class Migration_Controller {
    static $view_dir = null;
    static $ci = null;
    function __construct ($config = array()) {
        if ( ! $config['enabled']) {
            show_404();
            exit;
        }

        if (is_null(self::$ci)) {
            self::$ci =& get_instance();
        }
        self::$ci =& get_instance();
        self::$ci->load->library('migrations');
        self::$ci->load->database();
        self::$ci->load->dbforge();

        $backtrace = debug_backtrace();

        $method = $backtrace[6]['args'][0];
        $arguments = $backtrace[6]['args'][1];
        $this->controller_url = self::$ci->uri->segment(1);
        $this->spark_version = basename(dirname(dirname(__FILE__)));
        if (is_null(Migration_Controller::$view_dir)) {
            Migration_Controller::$view_dir = "../../sparks/migration_tool/{$this->spark_version}/views";
        }
        $this->view_dir = Migration_Controller::$view_dir;


        if (method_exists($this, $method)) {
            call_user_func_array(array($this, $method), $arguments);
        } else {
            show_404();
            exit;
        }
    }

    function _load_view ($filename, $vars = array(), $return = false) {
        return self::$ci->load->view("{$this->view_dir}/{$filename}", $vars, $return);
    }

    function index () {
        $this->database_empty = self::$ci->migrations->is_database_empty();
        $this->migrations = self::$ci->migrations->all;

        $this->jquery = $this->_load_view('jquery.js', array(), true);
        $this->style = $this->_load_view('style.css', $this, true);
        $this->left_column = $this->_load_view('migration_list.html.php', $this, true);
        $this->right_column = $this->_load_view('right_panel.html.php', $this, true);
        $this->_load_view('index.html.php', $this);
    }

    function new_migration () {
        Migration::create($_POST['migration_name']);
        redirect(site_url($this->controller_url));
    }

    function run () {
        #die('<pre>'.print_r($_POST,true));
        self::$ci->migrations->to_version($_POST['version']);
        redirect(site_url($this->controller_url));
    }
}
