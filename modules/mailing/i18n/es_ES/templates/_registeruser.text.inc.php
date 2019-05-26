Buen d&iacute;a <?php echo $user->getBuddyname(); ?>,
	Una cuenta registrada con el nombre '<?php echo $user->getUsername(); ?>' en The Bug Genie, aqu&iacute; : <?php echo $module->generateURL('home'); ?>.

Para poder utilizar la nueva cuenta, debe confirmarla a través del siguiente enlace:<br>
<?php echo $link_to_activate; ?>

Su contrase&ntilde;a es : <?php echo $password; ?>
y se puede conectar con est&aacute; contrase&ntilde;a en el enlace anterior.

(Este correo fue enviado a su direcci&oacute;n por una solicitud. Si no ha guardado la cuenta de usuario, o cree que ha recibido este mensaje por error, b&oacute;rrelo. Pedimos disculpas por las molestias)
