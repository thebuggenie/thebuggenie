<b>Setting up SVN integration</b><br>
To set up SVN integration, follow these three steps:
<ul>
	<li><b>Add post-commit hook to SVN repository</b><br>
	In the module directory, there is a post-commit.sh script that needs to be triggered upon an SVN commit. If you don't use any other commit-hooks, this script can replace the default post-commit script. If you already have a working post-commit.sh script, copy the contents of that file into your existing post-commit.sh script.<br>
	<b>Remember to remove the post-commit.sh from the module directory, or make it inaccessible from the web</b><br>
	<br>
	If you don't know how to set this up, please consult the SVN manual.<br><br></li>
	<li><b>Edit post-commit script</b><br>
	Make sure you edit the script so the correct path is used. If you are going to use the web-update, make sure the settings in the file matches the settings on the SVN integration configuration page.<br><br></li>
	<li><b>Set up ViewVC</b><br>
	ViewVC is a web interface for browsing repositories. To use this, please set it up according to the instructions on the <a href="http://www.viewvc.org" target="_blank">ViewVC website</a>.<br>
	<br>
	When ViewVC is set up, make sure you set up the URLs to ViewVC in the SVN integration configuration panel.
</ul>
<br>
When you have followed the three steps above, you should be able to use the SVN integration module.<br>
<br>
To learn more about how to use the SVN integration properly, please see <a href="help.php?topic=svn_integration/main"><b>Using SVN integration</b></a>.