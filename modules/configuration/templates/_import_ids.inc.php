								<div class="tab_header"><?php echo __('Data for project import'); ?></div>
								<div class="csv_data_box">
									<div class="header"><?php echo __('Issue type schemes'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGIssuetypeScheme::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<div class="csv_data_box">
									<div class="header"><?php echo __('Workflow schemes'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGWorkflowScheme::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<div class="tab_header"><?php echo __('Data for issue import'); ?></div>
								<div class="csv_data_box">
									<div class="header"><?php echo __('Projects and milestones'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGProject::getAll() as $item): ?>
											<tr><th colspan="2"><?php echo $item->getName(); ?>: <?php echo __('ID'); ?> <?php echo $item->getID(); ?></th></tr>
											<?php foreach ($item->getMilestones() as $item2): ?>
												<tr><td><?php echo $item2->getName(); ?></td><td><?php echo $item2->getID(); ?></td></tr>
											<?php endforeach; ?>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<div class="csv_data_box">
									<div class="header"><?php echo __('Issue type values'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGIssuetype::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<div class="csv_data_box">
									<div class="header"><?php echo __('Status values'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGStatus::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<br class="clear" />
								<div class="csv_data_box">
									<div class="header"><?php echo __('Reproducability values'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGReproducability::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<div class="csv_data_box">
									<div class="header"><?php echo __('Severity values'); ?></div>
										<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGSeverity::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<div class="csv_data_box">
									<div class="header"><?php echo __('Category values'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGCategory::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<br class="clear" />
								<div class="csv_data_box">
									<div class="header"><?php echo __('Priority values'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGPriority::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<div class="csv_data_box">
									<div class="header"><?php echo __('Resolution values'); ?></div>
									<table class="cleantable">
										<thead>
											<tr>
												<th><?php echo __('Name'); ?></th>
												<th><?php echo __('ID'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach (TBGResolution::getAll() as $item): ?>
											<tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>