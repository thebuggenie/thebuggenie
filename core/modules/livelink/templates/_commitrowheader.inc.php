<?php

    /** @var \thebuggenie\core\entities\Commit $commit */
    /** @var \thebuggenie\core\entities\Project $project */

?>
<div class="row-header">
    <?= tbg_formatTime($commit->getDate(), 20); ?>
</div>