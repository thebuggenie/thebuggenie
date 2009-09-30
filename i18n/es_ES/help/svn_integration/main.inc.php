Si no ha configurado SVN correctamente,<br>por favor vea <a href="help.php?topic=svn_integration/howto"><b>Configuración para la integración con SVN</b></a><br><br>
<b>Uso de la integración SVN</b><br>
EL módulo de integración SVN se utiliza en varios lugares:
<ul>
	<li><b>Transacciones SVN</b><br>
	Cuando esté enviando, la integración SVN verá dentro del comentario y actualizará cualquier problema referenciado.<br><br>El módulo buscará las siguientes palabras: <br>
	<i>fix, fixes, fixed, fixing, applies to, close, closes, references, ref, addresses, re, see, according to</i>, seguido por un <b>#</b> y el número de problema.<br>
	(Puede referenciar a tanntos problemas como quiera en el comentario del commit.)<br><br>
	<b>Ejemplo de comentariod de commit: </b><i>Fixing #B2-12, #B2-11 y #B2-10. También vea #B2-14.</i><br>
	Este comentario actualizará los cuatro problemas con la informació desde el commit, y publica los comentarios en todos los problemas.<br>
	<br>
	El m&acute;dulo de integración <i>no cierra</i> problemas automáticamente.<br><br></li>
	<li><b>Registro de commit SVN en problemas</b><br>
	Al ver los problemas, todos las transacciones de SVN estarán visibles al final del resumen, con enlaces al registro, diff y al archivo directamente (si ViewVC está configurado).<br><br></li>
	<li><b>"Ver código"</b><br>
	La página resumen del projecto tendrá un enlace "ver código" en la esquina superior derecha.<br><br></li>
</ul>
Si tiene sugerencias sobre como utilizar el módulo de integración SVN, háganoslo saber!
