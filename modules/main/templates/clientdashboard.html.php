<?php 

	$tbg_response->setTitle($client->getName());
	$tbg_response->setPage('client');
	$tbg_response->addBreadcrumb(link_tag(make_url('client_dashboard', array('client_id' => $client->getID())), $client->getName()));
	
?>

<div class="client_dashboard">
	<div class="dashboard_client_info">
		<span class="dashboard_client_header"><?php echo $client->getName(); ?></span><br />
		<b><?php echo __('Website:'); ?></b> <?php if ($client->getWebsite() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="<?php echo $client->getWebsite(); ?>" target="_blank"><?php echo $client->getWebsite(); ?></a><?php endif; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo __('Email address:'); ?></b> <?php if ($client->getEmail() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="mailto:<?php echo $client->getEmail(); ?>" target="_blank"><?php echo $client->getEmail(); ?></a><?php endif; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo __('Telephone:'); ?></b> <?php if ($client->getTelephone() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getTelephone(); endif; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo __('Fax:'); ?></b> <?php if ($client->getFax() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getFax(); endif; ?>
		<br /><a href="javascript:void(0);" onclick="showFadedBackdrop('<?php echo make_url('get_partial_for_backdrop', array('key' => 'client_users', 'client_id' => $client->getID())); ?>');"><?php echo __('View users'); ?></a>
		<br />
	</div>
	
	<div class="header">
		<?php echo __('Projects for %client%', array('%client%' => $client->getName())); ?>
	</div>
	<?php if (count(TBGProject::getAllByClientID($client->getID())) > 0): ?>
		<ul class="project_list simple_list">
		<?php foreach (TBGProject::getAllByClientID($client->getID()) as $aProject): ?>
			<li><?php include_component('project/overview', array('project' => $aProject)); ?></li>
		<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p class="content faded_out"><?php echo __('There are no projects assigned to this client'); ?>.</p>
	<?php endif; ?>
</div>