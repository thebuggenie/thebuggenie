<li id="scope_settings">
    <h1>Scope settings</h1>
    <div class="log">
        <?php foreach ($scope_settings as $module => $settings): ?>
            <h3><?php echo $module; ?></h3>
            <table style="border: 0;" cellpadding="0" cellspacing="0">
                <?php foreach ($settings as $setting => $setting_details): ?>
                    <tr>
                        <td><b><?php echo $setting; ?>: </b></td>
                        <td>
                            <?php foreach ($setting_details as $uid => $setting): ?>
                                <?php echo htmlspecialchars($setting); ?>&nbsp;<i style="color: #AAA;">(<?php echo (!$uid) ? 'default' : "uid {$uid}"; ?>)</i><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endforeach; ?>
    </div>
</li>
