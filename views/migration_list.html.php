<?php foreach ($migrations as $migration): ?>
    <table class="migration-table">
        <tr>
            <?php if (! $migration->is_current): ?>
            <td>
                <form action="<?php echo site_url("{$controller_url}/run") ?>" method="post">
                    <input type="hidden" name="version" value="<?php echo $migration->timestamp ?>">
                    <input type="submit" value="Migrate" />
                </form>
            </td>
            <?php else: ?>
                <td><input type="submit" value="Migrate" disabled="disabled" /></td>
            <?php endif ?>
            <?php if ($migration->is_empty): ?>
                <td class="<?php if ($migration->is_current): ?> current-migration <?php endif ?>">
                    Empty Database
                </td>
            <?php else: ?>
                <td class="<?php if ($migration->is_current): ?> current-migration <?php endif ?>">
                    <span class="time-display time-is-<?php echo $migration->timestamp ?>">
                        <?php echo $migration->formatted_timestamp ?> UTC
                    </span> - <?php echo $migration->name ?>
                    <pre class="source closed"><?php echo htmlspecialchars($migration->content, ENT_QUOTES) ?></pre>
                </td>
            <?php endif ?>
        </tr>
    </table>
<?php endforeach ?>
