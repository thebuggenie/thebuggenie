<b>Configuración para la integración con SVN</b><br>
Para configurar la integración con SVN, siga estos tres pasos:
<ul>
	<li><b>Agregue un gancho post-commit al repositorio SVN</b><br>
	En el directorio module, hay un ejecutable post-commit.sh que necesita ser ejecutado al efectuar un commit en SVN. Si no utiliza otro gancho commit, este ejecutable puede reemplazar al ejecutable por defecto post-commit. Si ya está trabajando con post-commit.sh, copie el contenido del archivo en su post-commit.sh.<br>
	<b>Recuerde eliminar post-commit.sh de la carpeta module, o hágalo inaccesible desde la web</b><br>
	<br>
	Si no sabe como configurarlo, consulte el manual de SVN, por favor.<br><br></li>
	<li><b>Edite el ejecutable post-commit</b><br>
	Asegurese que edita el ejecutable correcto. Si va a usar la actualización web, asegurese que la configuracón en el archivo coincide con la confuguración de SVN en la página de configuración de integración.<br><br></li>
	<li><b>Configure ViewVC</b><br>
	ViewVC es una interfaz web para navegar repositorios. Para utilizarlo, por favor siga las instrucciones del <a href="http://www.viewvc.org" target="_blank">sitio web ViewVC</a>.<br>
	<br>
	Cuando ViewVC esté listo, asegurese de poner las URLs a ViewVC en el panel de configuración de integración SVN.
</ul>
<br>
Si Ud. siguió los tres pasos de arriba, estará listo para utilizar el módulo de integración SVN.<br>
<br>
Para aprender más acerca de como utilizar la integración SVN apropiadamente, por favor vea <a href="help.php?topic=svn_integration/main"><b>Uso de la integración SVN</b></a>.
