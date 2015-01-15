<table style="width: 100%; height: 100%;" cellpadding="0" cellspacing="0"<?php if (isset($table_id)): ?> id="<?php echo $table_id; ?>"<?php endif; ?> class="project_info_container">
    <tr>
        <td class="project_information_sidebar" id="project_information_sidebar">
            <div class="sidebar_links">
                <?php include_component('project/projectinfolinks'); ?>
            </div>
        </td>
        <td class="project_information_main">
