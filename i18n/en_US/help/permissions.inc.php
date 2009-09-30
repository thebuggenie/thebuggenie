Contrary to BUGS, BUGS 2 has a very powerful permissions system. Easy to use and get familiar with, still very flexible.<br>
<br>
Permissions in BUGS 2 is set on 4 levels (in order of importance):
<ul>
	<li>For a specific user</li>
	<li>For team members</li>
	<li>For members of groups</li>
	<li>For everyone</li>
</ul>
Permissions are defined first for "everyone", then for a group, then a team, and then for a specific user. Usually, the first two will be enough - however, it is important to know in what order permissions are applied:
<ul>
	<li>User permissions override anything else, since this is user-specific</li>
	<li>Team permissions override group permissions</li>
	<li>Group permissions override the "Everyone" permissions</li>
	<li>The "Everyone" permission is the basic permission set, and is overriden by anything else</li>
</ul>
When defining permissions, you have colored "flags" that tell you what permission is granted for that specific item:
<div style="padding: 10px; margin-left: 15px;">
	<p><img src="themes/oxygen/led_green.png"> - <b>Full access to this item</b>. For items with read/write access, green means you have read and write access.</p>
	<p><img src="themes/oxygen/led_yellow.png"> - <b>Limited access to this item</b> (only for items with read/write access) - means you have only read access.</p>
	<p><img src="themes/oxygen/led_red.png"> - <b>No access to this item</b></p>
	<p><img src="themes/oxygen/led_lightblue.png"> - Permission not explicitly granted for this user/team/group. This flag means that access is defined on a lower level.</p>
</div>
First, you want to set up what "Everyone" can do. You can do this from <b>Configuration center &ndash;&gt; Manage teams &amp; groups</b>. The permissions set in the "Everyone" group initially defines what a user can do or have access to. After defining what "Everyone" can do, you move on to what users of the different groups should be able to do. By default, you have one "Admin"-group, one "Guest"-group and one "Users"-group. Select one of these groups to set permissions for users in this group.<br>
<br>
Individual user permissions are set via <b>Configuration center &ndash;&gt; Manage users</b>. Find a user, and click the "Permissions" link on that user.<br>
<br>
<b>Now, let's use an example</b><br>
Users in the "Admin"-group should have access to the Configuration section. Select the "Admin" group, then click on the blue "Configuration center" icon. This grants all users in the "Admin"-group access to the Configuration center link in the top menu, and the Configuration center page.<br>
<br>
Now, move on to giving access to different Configuration sections, by clicking on Configuration center. Now click the blue buttons for each section you want to provide <i>read</i> access to. Clicking the (now yellow) button again, also provides <i>write</i> access to that section. Clicking the (now green) button explicitly denies that user access to the specified section (this is usually only necessary when access is already given on a lower level). Do this for the "Manage users" and "Scopes" section.<br>
<br>
Say you want to only provide user and scope administration capabilities to "Staff members". Make sure you haven't specifically granted access to "Configuration center" -> "Manage users" to any group (including the "Everyone" group) or user. Now, create a "Staff members" <i>team</i>, and select it by clicking on it. Now, select the Configuration center subsection by clicking on it (the buttons should be all blue). Now click the "Manage users" permission icon twice, which turns it green. Do the same for "Scopes".<br>
<br>
<b>Congratulations!</b><br>
Now, users in the admin group have access to all sections in the Configuration center, except "Manage users" and "Scopes", which is only available to users in the "Staff members" team.<br>
<br>
Remember - users can only be part of one group, but can be part of several teams. Also remember that "deny" access settings only applies unless "allow" access is given on the same, or a higher level. You can read more about permission levels and permission overriding in the beginning of this help topic.