#!/bin/sh

REPOS="$2"
REV="$1"

SVNLOOK=/usr/bin/svnlook
PHP=/usr/bin/php

COMMIT_MSG=`$SVNLOOK log -r $REV "$REPOS"`
CHANGED=`$SVNLOOK changed -r $REV "$REPOS"`
AUTHOR=`$SVNLOOK author -r $REV "$REPOS"`
URL_COMMIT_MSG=`$PHP -r 'echo urlencode($argv[1]);' "$COMMIT_MSG"`

$PHP /var/www/thebuggenie/modules/svn_integration/post-commit.php $AUTHOR $REV "$COMMIT_MSG" "$CHANGED"

# if you cannot run the command above either via ssh or locally, try using the url below.
# remember to set the svn_passkey to something unique on the server, as the url is easily accessible from the outside. 
wget --no-check-certificate "http://www.server.com/thebuggenie/modules/svn_integration/post-commit.php?passkey=svn_passkey&author=${AUTHOR}&rev=${REV}&commit_msg=${URL_COMMIT_MSG}&changed=${CHANGED}" -o /dev/null -O /dev/null