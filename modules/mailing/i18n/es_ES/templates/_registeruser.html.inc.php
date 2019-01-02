<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #646464;">
    Buen d&iacute;a <?php echo $user->getBuddyname(); ?>,<br>
    Una cuenta registrada con el nombre <b><?php echo $user->getUsername(); ?></b> en The Bug Genie, aqu&iacute; : <?php echo $module->generateURL('home'); ?>.<br>
    <br>
    Para poder utilizar la nueva cuenta, debe confirmarla a trav�s del siguiente enlace:<br>
    <a href="<?php echo $link_to_activate; ?>"><?php echo $link_to_activate; ?></a><br>
    <br>
    Su contrase&ntilde;a es : <b><?php echo $password; ?></b><br>
    y se puede conectar con est&aacute; contrase&ntilde;a en el enlace anterior.<br>
    <br>
    (Este correo fue enviado a su direcci&oacute;n por una solicitud. Si no ha guardado la cuenta de usuario, o cree que ha recibido este mensaje por error, b&oacute;rrelo. Pedimos disculpas por las molestias)
</div>
