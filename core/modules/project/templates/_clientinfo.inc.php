<span class="project_client_header"><a href="<?php echo make_url('client_dashboard', array('client_id' => $client->getID())); ?>"><?php echo $client->getName(); ?></a></span><span class="project_client_viewusers"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'client_users', 'client_id' => $client->getID())); ?>');"><?php echo __('View users'); ?></a></span><br />
<table>
    <tr>
        <td style="padding-right: 10px">
            <b><?php echo __('Website:'); ?></b> <?php if ($client->getWebsite() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="<?php echo $client->getWebsite(); ?>" target="_blank"><?php echo $client->getWebsite(); ?></a><?php endif; ?>
        </td>
        <td style="padding: 0px 10px">
            <b><?php echo __('Email address:'); ?></b> <?php if ($client->getEmail() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="mailto:<?php echo $client->getEmail(); ?>" target="_blank"><?php echo $client->getEmail(); ?></a><?php endif; ?>
        </td>
        <td style="padding: 0px 10px">
            <b><?php echo __('Telephone:'); ?></b> <?php if ($client->getTelephone() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getTelephone(); endif; ?>
        </td>
        <td style="padding: 0px 10px">
            <b><?php echo __('Fax:'); ?></b> <?php if ($client->getFax() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getFax(); endif; ?>
        </td>
    </tr>
</table>
