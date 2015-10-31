<?php if ($item instanceof \thebuggenie\core\entities\LogItem): ?>
    <li>
        <span class="date"><?php echo (date('YmdHis', $previous_time) != date('YmdHis', $item->getTime())) ? tbg_formatTime($item->getTime(), 6) : ''; ?></span>&nbsp;
        <?php

            $previous_value = null;
            $new_value = null;
            try
            {
                switch($item->getChangeType())
                {
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CREATED:
                        echo image_tag('icon_open_new.png');
                        echo __('The issue was created');
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CLOSE:
                        echo image_tag('icon_close.png');
                        echo __('The issue was closed');
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_REOPEN:
                        echo image_tag('icon_open_new.png');
                        echo __('The issue was reopened');
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_DEPENDS:
                        echo image_tag('icon_new_related_issue.png');
                        echo __('The issues dependency changed: %change', array('%change' => '<strong>' . $item->getText() . '</strong>'));
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_UPDATE:
                        echo __('The issue was updated: %change', array('%change' => '<strong>' . $item->getText() . '</strong>'));
                        if (trim($item->getPreviousValue()) || trim($item->getCurrentValue()))
                        {
                            echo '<br>';
                            echo $item->getPreviousValue() . ' &rArr; ' . $item->getCurrentValue();
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_UPDATE_TITLE:
                        echo image_tag('icon_title.png');
                        echo __('Title updated: %previous_value => %new_value', array('%previous_value' => '<strong>' . $item->getPreviousValue() . '</strong>', '%new_value' => '<strong>' . $item->getCurrentValue() . '</strong>'));
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_UPDATE_DESCRIPTION:
                        echo image_tag('icon_description.png');
                        echo __('Description updated: %previous_value => %new_value', array('%previous_value' => '<strong>' . $item->getPreviousValue() . '</strong>', '%new_value' => '<strong>' . $item->getCurrentValue() . '</strong>'));
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_STATUS:
                        echo image_tag('icon_status.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\Status::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\Status::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Status changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_RESOLUTION:
                        echo image_tag('icon_resolution.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\Resolution::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\Resolution::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Resolution changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PRIORITY:
                        echo image_tag('icon_priority.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\Priority::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\Priority::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Priority changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_SEVERITY:
                        echo image_tag('icon_severity.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\Severity::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\Severity::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Severity changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_REPRODUCABILITY:
                        echo image_tag('icon_reproducability.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\Reproducability::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\Reproducability::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Reproducability changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_ISSUETYPE:
                        echo image_tag('icon_issuetype.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\Issuetype::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\Issuetype::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Issuetype changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CATEGORY:
                        echo image_tag('icon_category.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\Category::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\Category::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Category changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_MILESTONE:
                        echo image_tag('icon_milestone.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\Milestone::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\Milestone::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Milestone changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_OWNED:
                        echo image_tag('icon_user.png');
                        if ($item->hasChangeDetails())
                        {
                            echo __("Owned by changed to %user", array('%user' => '<strong>'.$item->getText().'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_POSTED:
                        echo image_tag('icon_user.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\User::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\User::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("Posted by changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CUSTOMFIELD_CHANGED:
                        echo image_tag('icon_customdatatype.png');
                        if ($item->hasChangeDetails())
                        {
                            $key_data = explode(':', $item->getText());
                            $key = $key_data[0];
                            $customdatatype = \thebuggenie\core\entities\CustomDatatype::getByKey($key);
                            if ($customdatatype instanceof \thebuggenie\core\entities\CustomDatatype)
                            {
                                $old_value = $item->getPreviousValue();
                                $new_value = $item->getCurrentValue();
                                switch ($customdatatype->getType())
                                {
                                    case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT:
                                    case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                    case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN:
                                        break;
                                    case \thebuggenie\core\entities\CustomDatatype::DATE_PICKER:
                                        $old_value = ($old_value != null) ? date('Y-m-d', (int)$old_value) : \thebuggenie\core\framework\Context::getI18n()->__('Not determined');
                                        $new_value = ($new_value != null) ? date('Y-m-d', (int)$new_value) : \thebuggenie\core\framework\Context::getI18n()->__('Not determined');
                                        break;
                                    case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                    case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                    case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                                    case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                                        $old_object = null;
                                        $new_object = null;
                                        try
                                        {
                                            switch ($customdatatype->getType())
                                            {
                                                case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                                    $old_object = \thebuggenie\core\entities\Edition::getB2DBTable()->selectById($old_value);
                                                    break;
                                                case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                                    $old_object = \thebuggenie\core\entities\Component::getB2DBTable()->selectById($old_value);
                                                    break;
                                                case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                                                    $old_object = \thebuggenie\core\entities\Build::getB2DBTable()->selectById($old_value);
                                                    break;
                                                case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                                                    $old_object = \thebuggenie\core\entities\Status::getB2DBTable()->selectById($old_value);
                                                    break;
                                            }
                                        }
                                        catch (\Exception $e) {}
                                        try
                                        {
                                            switch ($customdatatype->getType())
                                            {
                                                case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                                    $new_object = \thebuggenie\core\entities\Edition::getB2DBTable()->selectById($new_value);
                                                    break;
                                                case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                                    $new_object = \thebuggenie\core\entities\Component::getB2DBTable()->selectById($new_value);
                                                    break;
                                                case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                                                    $new_object = \thebuggenie\core\entities\Build::getB2DBTable()->selectById($new_value);
                                                    break;
                                                case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                                                    $new_object = \thebuggenie\core\entities\Status::getB2DBTable()->selectById($new_value);
                                                    break;
                                            }
                                        }
                                        catch (\Exception $e) {}
                                        $old_value = (is_object($old_object)) ? $old_object->getName() : \thebuggenie\core\framework\Context::getI18n()->__('Unknown');
                                        $new_value = (is_object($new_object)) ? $new_object->getName() : \thebuggenie\core\framework\Context::getI18n()->__('Unknown');
                                        break;
                                    default:
                                        $old_item = null;
                                        $new_item = null;
                                        try
                                        {
                                            $old_item = ($old_value) ? new \thebuggenie\core\entities\CustomDatatypeOption($old_value) : null;
                                        }
                                        catch (\Exception $e) {}
                                        try
                                        {
                                            $new_item = ($new_value) ? new \thebuggenie\core\entities\CustomDatatypeOption($new_value) : null;
                                        }
                                        catch (\Exception $e) {}
                                        $old_value = ($old_item instanceof \thebuggenie\core\entities\CustomDatatypeOption) ? $old_item->getName() : \thebuggenie\core\framework\Context::getI18n()->__('Unknown');
                                        $new_value = ($new_item instanceof \thebuggenie\core\entities\CustomDatatypeOption) ? $new_item->getName() : \thebuggenie\core\framework\Context::getI18n()->__('Unknown');
                                        break;
                                }
                                echo __("%field_name changed: %previous_value => %new_value", array('%field_name' => $customdatatype->getName(), '%previous_value' => '<strong>'.$old_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                            }
                            else 
                            {
                                echo __('Custom field changed');
                            }
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PAIN_BUG_TYPE:
                        echo image_tag('icon_priority.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = $item->getPreviousValue() ? \thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_bug_type', $item->getPreviousValue()) : __('Not determined');
                            $new_value = $item->getCurrentValue() ? \thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_bug_type', $item->getCurrentValue()) : __('Not determined');
                            echo __("Pain bug type on issue changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PAIN_EFFECT:
                        echo image_tag('icon_priority.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = $item->getPreviousValue() ? \thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_effect', $item->getPreviousValue()) : __('Not determined');
                            $new_value = $item->getCurrentValue() ? \thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_effect', $item->getCurrentValue()) : __('Not determined');
                            echo __("Pain effect on issue changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PAIN_LIKELIHOOD:
                        echo image_tag('icon_priority.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = $item->getPreviousValue() ? \thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_likelihood', $item->getPreviousValue()) : __('Not determined');
                            $new_value = $item->getCurrentValue() ? \thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_likelihood', $item->getCurrentValue()) : __('Not determined');
                            echo __("Likelihood on issue changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PAIN_CALCULATED:
                        echo image_tag('icon_percent.png');
                        if ($item->hasChangeDetails())
                        {
                            echo __("Calculated pain on issue changed: %value", array('%value' => '<strong>'.$item->getText().'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_USERS:
                        echo image_tag('icon_user.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = ($item->getPreviousValue()) ? (($old_item = \thebuggenie\core\entities\User::getB2DBTable()->selectById($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
                            $new_value = ($item->getCurrentValue()) ? (($new_item = \thebuggenie\core\entities\User::getB2DBTable()->selectById($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
                            echo __("User working on issue changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_ASSIGNED:
                        echo image_tag('icon_user.png');
                        echo __("Assignee changed to %new_value", array('%new_value' => '<strong>'.$item->getText().'</strong>'));
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_TIME_SPENT:
                        echo image_tag('icon_time.png');
                        echo __("Time spent changed: %value", array('%value' => '<strong>'.$item->getText().'</strong>'));
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PERCENT:
                        echo image_tag('icon_percent.png');
                        if ($item->hasChangeDetails())
                        {
                            echo __("Percent complete changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.(int) $item->getPreviousValue().'</strong>', '%new_value' => '<strong>'.(int) $item->getCurrentValue().'</strong>'));
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_BLOCKED:
                        echo image_tag('icon_locked.png');
                        echo __('Blocking status changed: %value', array('%value' => '<strong>'. __('This issue is blocking the next release').'</strong>'));
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_UNBLOCKED:
                        echo image_tag('icon_unlocked.png');
                        echo __('Blocking status changed: %value', array('%value' => '<strong>'. __('This issue is no more blocking the next release').'</strong>'));
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_TIME_ESTIMATED:
                        echo image_tag('icon_estimated_time.png');
                        if ($item->hasChangeDetails())
                        {
                            echo __("Estimated time changed: %value", array('%value' => '<strong>'.$item->getText().'</strong>'));
                         }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_AFF_ADD:
                        echo image_tag('icon_affected_items.png');
                        if ($item->hasChangeDetails())
                        {
                            echo __("Affected item added: %value", array('%value' => '<strong>'.$item->getText().'</strong>'));
                         }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_AFF_UPDATE:
                        echo image_tag('icon_affected_items.png');
                        if ($item->hasChangeDetails())
                        {
                            echo __("Affected item updated: %value", array('%value' => '<strong>'.$item->getText().'</strong>'));
                         }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_AFF_DELETE:
                        echo image_tag('icon_affected_items.png');
                        if ($item->hasChangeDetails())
                        {
                            echo __("Affected time removed: %value", array('%value' => '<strong>'.$item->getText().'</strong>'));
                         }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_UPDATE_REPRODUCTIONSTEPS:
                        echo image_tag('icon_reproducability.png');
                        if ($item->hasChangeDetails())
                        {
                            $previous_value = $item->getPreviousValue() ? $item->getPreviousValue()  : __('Not determined');
                            $new_value = $item->getCurrentValue() ? $item->getCurrentValue() : __('Not determined');
                            echo __("Reproduction steps changed: %previous_value => %new_value", array('%previous_value' => '<strong>'.$previous_value.'</strong>', '%new_value' => '<strong>'.$new_value.'</strong>'));
                        }
                        break;
                    default:
                        echo $item->getChangeType();
                }
                if (!$item->hasChangeDetails())
                {
                    echo $item->getText();
                }
            }
            catch (\Exception $e)
            {
                echo __('Unknown change');
            }

        ?>
    </li>
<?php endif; ?>
