<?php

    use thebuggenie\core\entities\LogItem;

    /** @var LogItem $item */

?>
                <span class="user">
                    <?php if ($item->getUser() instanceof \thebuggenie\core\entities\User): ?>
                        <?php if ($item->getChangeType() != LogItem::ACTION_COMMENT_CREATED): ?>
                            <?php echo $item->getUser()->getNameWithUsername().':'; ?>
                        <?php else: ?>
                            <?php echo __('%user said', array('%user' => $item->getUser()->getNameWithUsername())).':'; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($item->getChangeType() != LogItem::ACTION_COMMENT_CREATED): ?>
                            <span class="faded"><?php echo __('Unknown user').':'; ?></span>
                        <?php else: ?>
                            <?php echo __('Unknown user said').':'; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
