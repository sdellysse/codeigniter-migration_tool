<form action="<?php echo site_url("{$controller_url}/run") ?>" method="post">
    <input type="hidden" name="version" value="latest"/>
    <input type="submit" value="Migrate to Latest" style="width: 100%">
</form>
<form action="<?php echo site_url("{$controller_url}/new_migration") ?>" method="post" id="new-migration">
    <fieldset>
        <legend>Create new migration</legend>
        <label for="migration_name">Migration name:</label>
        <input type="text" name="migration_name" id="migration_name" />
        <input type="submit" value="Create" />
    </fieldset>
</form>
