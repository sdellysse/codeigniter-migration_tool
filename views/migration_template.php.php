<<?php ?>?php defined('BASEPATH') or die('No direct script access allowed.');

class <?php echo $class_name ?> extends AbstractMigration {
  function up () {
    $this->dbforge->add_key('id', TRUE);

    $this->fields = array(
      'id' => array( 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE, 'null' => FALSE),
      #'' => array( 'type' => 'varchar', 'constraint' => 255, 'null' => false),
<?php if (isset($fields)): ?>      <?php foreach ($fields as $field): ?>'<?php echo $field['name'] ?>' => array(<?php foreach ($attrs as $attr): ?><?php echo $attr['name']?> => <?php echo $attr['value'] ?>, <?php endforeach ?>),<?php endforeach ?><?php endif ?>
    );

    #$this->dbforge->add_field($this->field);
    #$this->dbforge->create_table('', true);
  }

  function down () {
    #$this->dbforge->drop_table('');
  }
}
