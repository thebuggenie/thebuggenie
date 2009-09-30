A diferencia de BUGS, BUGS 2 tiene un sistema de permisos muy poderoso. Fácil de usar y familiarizarse, y muy flexible.<br>
<br>
Los permisos en BUGS 2 se dividen en 4 niveles (en orden de importancia):
<ul>
	<li>Para un usuario en particular</li>
	<li>Para miembros de un equipo</li>
	<li>Para miembros de un grupo</li>
	<li>Para todos</li>
</ul>
Los permisos inicialmente están definidos para "todos", luego para un grupo, luego para un equipo, y luego un usuario particular. Normalmente, los primeros dos serán suficientes, sin embargo, es importante conocer el orden en que los permisos son aplicados:
<ul>
	<li>Los permisos de usuario prevalecen sobre el resto, ya que éste es un usuario particular</li>
	<li>Los permisos de equipo prevalecen sobre los de grupo</li>
	<li>Los permisos de grupo sobre los permisos de "Todos"</li>
	<li>El permiso "Todos" es el permiso básico, y puede ser sobreescrito por cualquier otro</li>
</ul>
Cuando se definen los permisos, hay  "botones" de colores que indican que permiso está asignado en cada ítem:
<div style="padding: 10px; margin-left: 15px;">
	<p><img src="themes/default/led_green.png"> - Acceso completo a un item específico. Para los items con acceso de lectura/escritura, verde significa que se puede leer y escribir.</p>
	<p><img src="themes/default/led_yellow.png"> - Acceso limitado a un item particular - Solo para items con acceso de lectura/escritura - siginfica que se puede leer.</p>
	<p><img src="themes/default/led_red.png"> - Sin acceso a un item particular.</p>
	<p><img src="themes/default/led_lightblue.png"> - Permiso no concedido expresamente para este usuario/equipo/grupo. Cuando hay un icono azul, el acceso está definido en un nivel más bajo.</p>
</div>
Primero, desea configurar lo que  "Todos" pueden hacer. Esto lo puede hacer desde <b>Centro de configuración &ndash;&gt; Gestión de equipos &amp; grupos</b>. Los permisos del grupo inicialmente están en "Todos"  esto define lo que un usuario puede hacer o a lo que tiene acceso. Después de definir lo que "Todos" pueden hacer,  se pasa a lo que los usuarios de diferentes grupos deben ser capaces de hacer. Por defecto, hay un grupo  "Administrador", un grupo "Invitado" y un grupo de "Usuarios". Seleccione uno de estos grupos para establecer los permisos de los usuarios.<br>
<br>
Los permisos de usuarios individuales se establecen a través de <b>Centro de configuración &ndash;&gt; Gestión de usuarios</b>. Busque un usuario, y haga clic sobre el enlace "Permisos" de ese usuario.<br>
<br>
<b>Ahora, usemos un ejemplo</b><br>
Los usuarios del grupo "Administradores" debe tener acceso a la sección de Configuración. Selecciones el grupo  "Administrador",  luego clic en el icono azul del "Centro de Configuración". Esto otorga a todos los usuarios en el grupo "Administrador" acceso al enlace del menú superior al Centro de Configuración, y a la página del Centro de Configuración.<br>
<br>
Ahora, pasemos a dar acceso a diferentes secciones de la Configuración, haciendo clic en Centro de Configuración. Ahora haga clic en los botones azules de cada sección que quiere dar permiso de <i>lectura</i>. Haciendo clic en el botón (ahora amarillo) nuevamente, también da acceso de <i>escritura</i> a esa sección. Haciendo clic en el botón (ahora verde) deniega explícitamente que el usuario acceda a una sección específica  (normalmente esto es necesario solo cuando el acceso ya se dio en un nivel inferior). Para hacer esto use las secciones  "Gestión de Usuarios" y "Ambitos de BUGS 2".<br>
<br>
Supongamos que quiere dar administración de usuarios y ámbitos a un miembro de "Miembros del Personal". Asegurese que no tiene otorgados permisos específicos en "Centro de Configuracion" -> "Gestión de Usuarios" a cualquier grupo (incluyendo al grupo "Todos") o usuario. Ahora, cree en <i>Equipo</i>  "Miembro del Personal" , y seleccionelo haciendo clic en él. Ahora, seleccione la subsección del Centro de Configuración (los botones deben estar todos azules). Ahora haga clic en el icono de permisos "Gestión de usuarios" dos veces, lo que los pone en verde. haga lo mismo para "Ambitos de BUGS 2".<br>
<br>
<b>Felicitaciones!</b><br>
Ahora, los usuarios del grupo administrador tienen acceso a todas las secciones en el Centro de Configuración, excepto a "Gestión de Usuarios" y "Ambitos de BUGS 2", lo cual está disponible solo para el equipo "Miembros del personal" .<br>
<br>
Recuerde: los usuarios solo pueden ser parte de un grupo, pero pueden ser parte de varios equipos. También recuerde que la opción acceso "denegado"  sólo se aplica si el acceso "permitido" esta dado en el mismo, o un nivel superior. Puede leer más acerca de niveles de permisos o privilegios de permisos al principio de esta ayuda.
