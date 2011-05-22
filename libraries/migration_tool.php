<?php defined('BASEPATH') or die('No direct script access allowed');

class Migration_Tool {
    function __construct ($config = array()) {
        if ( ! $config['enabled']) {
            show_404();
            exit;
        }

        $this->CI =& get_instance();
        $this->CI->load->library('migration');

        $backtrace = debug_backtrace();

        $method = $backtrace[6]['args'][0];
        $arguments = $backtrace[6]['args'][1];
        $this->controller_url = $this->CI->uri->segment(1);
        $this->spark_version = basename(dirname(dirname(__FILE__)));
        $this->view_dir = "../../sparks/migration_tool/{$this->spark_version}/views";


        if (method_exists($this, $method)) {
            call_user_func_array(array($this, $method), $arguments);
        } else {
            show_404();
            exit;
        }
    }

    function _load_view ($filename, $vars = array(), $return = false) {
      return $this->CI->load->view("{$this->view_dir}/{$filename}", $vars, $return);
    }

    function index () {
        $this->migrations_list = array();
        foreach ($this->CI->migration->all_filenames() as $filename) {
          $this->migrations_list []= $this->CI->migration->info($filename);
        }

        $this->database_empty = $this->CI->migration->is_database_empty();

        $this->jquery = $this->_load_view('jquery.js', array(), true);
        $this->style = $this->_load_view('style.css', $this, true);
        $this->left_column = $this->_load_view('migration_list.html.php', $this, true);
        $this->right_column = $this->_load_view('right_panel.html.php', $this, true);
        $this->_load_view('index.html.php', $this);
    }

    function new_migration () {
      $this->CI->migration->create($_POST['migration_name']);
      redirect(site_url($this->controller_url));
    }
}
