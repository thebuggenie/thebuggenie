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
                                        <?php foreach (\thebuggenie\core\entities\IssuetypeScheme::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\WorkflowScheme::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\Project::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\Issuetype::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\Status::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\Reproducability::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\Severity::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\Category::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\Priority::getAll() as $item): ?>
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
                                        <?php foreach (\thebuggenie\core\entities\Resolution::getAll() as $item): ?>
                                            <tr><td><?php echo $item->getName(); ?></td><td><?php echo $item->getID(); ?></td></tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
