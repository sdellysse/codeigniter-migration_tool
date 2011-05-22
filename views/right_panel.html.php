<form action="<?php echo site_url("{$controller_url}/new_migration") ?>" method="post">
  <fieldset>
    <legend>Create new migration</legend>
    <label for="migration_name">Migration name:</label>
    <input type="text" name="migration_name" id="migration_name" />
    <input type="submit" value="Create" />
  </fieldset>
</form>
