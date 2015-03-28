<?php

    $found = false;
    
    foreach ($editions as $releases)
    {
        if (array_key_exists(0, $releases))
        {
            $found = true;
            
            if ($releases[0]->getEdition() instanceof \thebuggenie\core\entities\Edition)
                echo '<div class="tab_header">'.$releases[0]->getEdition()->getName().'</div>';

            echo '<ul class="simple_list">'.get_component_html('project/release', array('build' => $releases[0])).'</ul>';
        }
    }
    
    if ($found == false)
    {
        ?><p class="content faded_out"><?php echo __('There are no downloadable releases at the moment'); ?></p><?php
    }
    
?>
