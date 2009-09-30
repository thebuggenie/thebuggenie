If you haven't already set up the SVN integration properly,<br>please see <a href="help.php?topic=svn_integration/howto"><b>Setting up SVN integration</b></a><br><br>
<b>SVN integration usage</b><br>
The SVN integration module integrates in several places:
<ul>
	<li><b>SVN commits</b><br>
	When you are committing, the SVN integration will look through the commit comment and update any issues referenced.<br><br>The module will look for the following words: <br>
	<i>fix, fixes, fixed, fixing, applies to, close, closes, references, ref, addresses, re, see, according to</i>, followed by a <b>#</b> and an issue number.<br>
	(You can reference as many issues as you want in a commit comment.)<br><br>
	<b>Commit comment example: </b><i>Fixing #B2-12, #B2-11 and #B2-10. Also see #B2-14.</i><br>
	This comment will update all four issues with the information from the commit, and post comments on all issues.<br>
	<br>
	The SVN integration module <i>does not</i> close issues automatically.<br><br></li>
	<li><b>SVN commit log on issues</b><br>
	When viewing issues, all SVN commits will be visible at the bottom of the summary, with quick links to the log, diff and file directly (if ViewVC is set up).<br><br></li>
	<li><b>"View code"</b><br>
	The project overview page will have a "view code" link in the top right corner.<br><br></li>
</ul>
If you have tips on how to use the SVN integration module further, let us know!