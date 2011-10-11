#!/usr/bin/env python

"""
Hook to submit pushed commits to The Bug Genie.

Note that this hook is implemented as a post_change_branch_tip hook rather than
a post_commit hook. This means that it can be used on a central repository and
will only submit commits when they are pushed, rather than when they are first
committed locally.

To install the hook, copy it to your bzr plugins directory. If the repository
will only be used by one user, the plugin can be placed in that user's
~/.bazaar/plugins directory. Otherwise, it will need to be placed in the global
plugins directory, which is &lt;bzrlib installation directory&gt;/plugins, e.g.
/usr/lib/python2.6/site-packages/bzrlib/plugins.

The hook must be configured with details of your installation of TBG. There are
two methods for submitting commit information. If your repository is hosted on
the same server as TBG, you should use the direct call method, which runs the
post-commit script on the server. In this case, the following configuration is
necessary (commands should be run in the repository directory on the server):

 $ bzr tbg-configure &lt;project ID&gt; direct &lt;full path to tbg_cli&gt; &lt;path to PHP binary&gt;

If this method cannot be used, then the script can be called over HTTP. In this
case, the following configuration is necessary:

 $ bzr tbg-configure &lt;project ID&gt; http &lt;report URL&gt; &lt;passkey set in module settings&gt;

The report URL being e.g. http://mydomain.com/thebuggenie/vcs_integration/report
without trailing slash.
"""

import sys, os, urllib
from bzrlib.branch import Branch, ChangeBranchTipParams
from bzrlib.commands import Command, register_command

if __name__ == '__main__':
	def info(msg):
		print '[INFO] %s' % (msg)
	def warning(msg):
		print '[INFO] %s' % (msg)
else:
	from bzrlib.trace import info, warning

## Class to submit revision details to The Bug Genie.
class TBGSubmitter:
	def execute(self, command):
		if self.dry_run:
			print command
		else:
			return os.system(command)

	def submit_direct(self, author, rev, message, date, files, oldrev):
		# Script must be executed from the TBG directory.
		prev_cwd = os.getcwd()
		os.chdir(os.path.dirname(self.opt1))
		ret = self.execute(
			'%s %s vcs_integration:report_commit %s "%s" "%s" "%s" "%s" '
			'"%s" %d &gt; /dev/null 2&gt;&amp;1' % (
				self.opt2, self.opt1, self.project, author.replace('"', '\\"'),
				rev, message.replace('"', '\\"'), files.replace('"', '\\"'),
				oldrev, date
			)
		)
		os.chdir(prev_cwd)

	def submit_http(self, author, rev, message, date, files, oldrev):
		return self.execute(
			'wget --no-check-certificate "%s/%s/?passkey=%s'
			'&amp;author=%s&amp;rev=%s&amp;commit_msg=%s&amp;changed=%s&amp;date=%d'
			'&amp;oldrev=%s" -o /dev/null -O /dev/null' % (
				self.opt1, self.project, self.opt2, urllib.quote(author),
				rev, urllib.quote(message), urllib.quote(files),
				date, oldrev
			)
		)


	## Construct the object.
	# @param method		Method to submit the revision ("direct" or
	#			"http").
	# @param opt1		If method is "direct", the path to the TBG
	#			commit script, else the report URL, for example:
	#			http://mydomain.com/thebuggenie/vcs_integration/report
	# @param opt2		If method is "direct", the path to the PHP
	#			binary to use to call the script, else the
	#			passkey configured in TBG.
	# @param project	Project ID.
	def __init__(self, method, opt1, opt2, project, dry_run = False):
		self.method = method
		self.opt1 = opt1
		self.opt2 = opt2
		self.project = project
		self.dry_run = dry_run

	## Submit a commit.
	# @param author		Author name.
	# @param rev		Revision number.
	# @param message	Commit message.
	# @param date		UNIX timestamp of the commit.
	# @param added		A Python list of files added.
	# @param removed	A Python list of files removed.
	# @param changed	A Python list of files changed.
	# @param oldrev		Previous revision of the commit.
	def submit(self, author, rev, message, date, added, removed, changed, oldrev):
		# Convert the supplied array of files into the format expected
		# by TBG.
		files = []
		[files.append("A %s" % (f)) for f in added]
		[files.append("D %s" % (f)) for f in removed]
		[files.append("U %s" % (f)) for f in changed]
		files = "\n".join(files)

		if self.method == "direct":
			return self.submit_direct(author, rev, message, date, files, oldrev)
		elif self.method == "http":
			return self.submit_http(author, rev, message, date, files, oldrev)

# Actual implementation of the hook.
def _tbg_hook_change_tip(params, dry_run = False):
	# Don't do anything if old revno is greater than new revno (i.e.
	# uncommit on central repo).
	if params.new_revno <= params.old_revno:
		return

	# Retrieve the configuration. Don't do anything if the project has not
	# been configured.
	config = params.branch.get_config()
	project = config.get_user_option('tbg_project')
	if not project:
		return
	method = config.get_user_option('tbg_method')
	opt1 = config.get_user_option('tbg_method_opt1')
	opt2 = config.get_user_option('tbg_method_opt2')

	# Create the submitter object.
	submitter = TBGSubmitter(method, opt1, opt2, project, dry_run)

	# Re-lookup the revision IDs from the revision numbers because for some
	# reason the IDs given in the parameters are not the correct IDs.
	params.old_revid = params.branch.get_rev_id(params.old_revno)
	params.new_revid = params.branch.get_rev_id(params.new_revno)

	# Iterate over each revision in the change set.
	revisions = params.branch.iter_merge_sorted_revisions(params.new_revid, params.old_revid, 'exclude', 'forward')
	for r in revisions:
		# Collect revision information.
		revision = params.branch.repository.get_revision(r[0])
		author = revision.get_apparent_authors()[0]
		delta = params.branch.repository.get_revision_delta(r[0])
		added = [f for (f,_,_) in delta.added]
		removed = [f for (f,_,_) in delta.removed]
		changed = [f for (f,_,_,_,_) in delta.modified]
		if r[1]:
			revno = "%d.%d.%d" % r[2]
		else:
			revno = str(r[2][0])

		# FIXME!!! Since I can't figure out how to get the real
		# previous revision number, I'm just gonna take the first part
		# of the number and take 1 from it.
		oldrev = int(r[2][0]) - 1

		# Submit the revision.
		info("Submitting revision %s to The Bug Genie." % (revno))
		submitter.submit(
			author, revno, revision.message, revision.timestamp,
			added, removed, changed, oldrev
		)

# Hook to submit pushed revisions to The Bug Genie.
def tbg_hook_change_tip(params):
	_tbg_hook_change_tip(params)
Branch.hooks.install_named_hook('post_change_branch_tip', tbg_hook_change_tip, 'The Bug Genie')

# Command to configure the TBG hook.
@register_command
class cmd_tbg_configure(Command):
	"""Configure the The Bug Genie hook."""

	takes_args = ['project', 'method', 'arg1', 'arg2']
	takes_options = []

	def run(self, project, method, arg1, arg2):
		if method != "direct" and method != "http":
			print "Invalid method (direct or http)"
			return 1

		br = Branch.open('.')
		config = br.get_config()
		config.set_user_option('tbg_project', project)
		config.set_user_option('tbg_method', method)
		config.set_user_option('tbg_method_opt1', arg1)
		config.set_user_option('tbg_method_opt2', arg2)
		return 0

# Code to test the hook.
if __name__ == '__main__':
	if len(sys.argv) != 4:
		print "Usage: %s &lt;path&gt; &lt;start num&gt; &lt;end num&gt;" % (sys.argv[0])
		print "Show what would be done to submit revisions from &lt;start num&gt; to &lt;end num&gt;, inclusive."
		sys.exit(1)

	branch = Branch.open(sys.argv[1])
	old_revno = int(sys.argv[2]) - 1
	new_revno = int(sys.argv[3])

	params = ChangeBranchTipParams(
		branch,
		old_revno,
		new_revno,
		branch.get_rev_id(old_revno),
		branch.get_rev_id(new_revno)
	)

	_tbg_hook_change_tip(params, True)
	sys.exit(0)