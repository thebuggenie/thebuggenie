Buen d&iacute;a <?php echo $user->getBuddyname()."\n"; ?>
Un pedido ha sido registrado para la regeneraci&otilde;n de su contrase&ntilde;a en <?php echo $module->generateUrl('home')."\n"; ?>

Para modificar su contrase&ntilde;a, haga clic en : <?php echo "\n".$module->generateUrl('reset_password', array('user' => $user->getUsername(), 'reset_hash' => $user->getActivationKey()))."\n"; ?>

Si Ud. no solicit&oacute; este correo, solo desc&aacute;rtelo. Nada ocurrir&aacute; a menos que haga clic en el enlace anterior.
