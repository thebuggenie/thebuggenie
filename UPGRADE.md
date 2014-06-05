UPGRADE
=======

NOTE: For the latest installation notes, troubleshooting and tips,
visit the FAQ: http://issues.thebuggenie.com/wiki/TheBugGenie:FAQ

IMPORTANT INFORMATION ABOUT UPGRADING TO THE BUG GENIE 3.3
----------------------------------------------------------

The Bug Genie 3.3 uses a dependancy resolution tool called composer, which must
be downloaded and run before The Bug Genie can be installed, upgraded or used.

Download and install composer.json from http://getcomposer.org before you continue.

After you have followed the instructions to download and install composer, run
`php composer.phar install`
from the main directory of The Bug Genie. Composer will download and install
all necessary components for The Bug Genie, and you can continue to the actual
upgrade as soon as it is completed.


upgrade via web
---------------

To upgrade The Bug Genie, do the following:
* Make sure the "installed" file contains the correct main version (3.2 for any 3.2.x versions, etc) of your setup (you can find this by visiting <thebuggenie.url>/about)
* Make a backup of your database
* If using file storage, also make a backup of your files/ folder
* Copy the content of the folder to your webserver
* Clear the files in the folders /core/cache/B2DB and /core/cache
* Create a file called "upgrade" in the directory where "installed" is located
* Make both "installed" and "upgrade" writable by the web-server

Then, visit the location <thebuggenie.url>/upgrade in your web-browser and the
upgrade wizard will start, and guide you through the upgrade process.

The version number in the "installed" file will be automatically updated

via command-line
----------------

Command-line upgrades are not supported.