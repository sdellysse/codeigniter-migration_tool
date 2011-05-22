<table class="migrations-table">
  <tr>
    <?php if (! $database_empty): ?>
      <td>
        <form action="<?php echo site_url("{$controller_url}/run") ?>" method="post">
          <input type="hidden" name="version" value="empty">
          <input type="submit" value="Migrate" />
        </form>
      </td>
    <?php endif ?>
    <td class="<?php if ($database_empty): ?>current-migration<?php endif ?>">
      Empty Database
    </td>
  </tr>
</table>
<?php foreach ($migrations_list as $migration): ?>
  <table class="migration-table">
    <tr>
      <?php if (! $migration['current']): ?>
        <td>
          <form action="<?php echo site_url("{$controller_url}/run") ?>" method="post">
            <input type="hidden" name="version" value="<?php echo $migration['timestamp'] ?>">
            <input type="submit" value="Migrate" />
          </form>
        </td>
      <?php endif ?>
      <td class="<?php if ($migration['current']): ?> current-migration <? endif ?>">
        <span class="time-display time-is-<?php echo $migration['timestamp'] ?>">
          <?php echo $migration['formatted_timestamp'] ?> UTC
        </span> - <?php echo $migration['name'] ?>
      </td>
    </tr>
  </table>
<?php endforeach ?>
