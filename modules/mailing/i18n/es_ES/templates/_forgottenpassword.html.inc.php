<h3>Olvid&oacute; su contrase&ntilde;a ?</h3>
<br>
<h4>Buen d&iacute;a <?php echo $user->getBuddyname(); ?></h4>
<p>
    Un pedido ha sido registrado para la regeneraci&oacute;n de su contrase&ntilde;a en <?php echo link_tag($module->generateUrl('home')); ?><br>
    Para modificar su contrase&ntilde;a, haga clic en : <?php echo link_tag($module->generateUrl('reset_password', array('user' => $user->getUsername(), 'reset_hash' => $user->getActivationKey()))); ?><br>
</p>
<br>
<div style="color: #888;">
    Si Ud. no solicit&oacute; este correo, solo desc&aacute;rtelo. Nada ocurrir&aacute; a menos que haga clic en el enlace anterior.
</div>
