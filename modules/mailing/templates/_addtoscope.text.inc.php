Hi, <?php echo $user->getBuddyname(); ?>!
Your user (<?php echo $user->getUsername(); ?>) registered at <?php echo $module->generateURL('home'); ?> has been added to a new scope in The Bug Genie.

Before you can log in to the new scope (located at the following URL(s): http://<?php echo join(', http://', $scope->getHostnames()); ?>, you need to confirm that you want to be added to that scope.

By accepting the scope membership, you're also granting read+write access to the user details registered in The Bug Genie to the scope administrator(s) in the new scope.
Don't worry, though, your main account will always be active and you can always disable the new scope access from your account page.

To accept (or reject) this invitation, go to <?php echo $module->generateURL('home'); ?> and log in to your account.
Then, on your account page, use the "Scope memberships" tab to manage your scope memberships.