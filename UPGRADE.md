UPGRADE
=======

NOTE: For the latest installation notes, troubleshooting and tips,
visit the FAQ: http://issues.thebuggenie.com/wiki/TheBugGenie:FAQ

IMPORTANT INFORMATION ABOUT UPGRADING FROM THE BUG GENIE 3.2
----------------------------------------------------------

The Bug Genie 4.0 uses a dependancy resolution tool called composer, which must
be downloaded and run before The Bug Genie can be installed, upgraded or used.

Download and install composer.json from http://getcomposer.org before you continue.

After you have followed the instructions to download and install composer, run
`php composer.phar install`
from the main directory of The Bug Genie. Composer will download and install
all necessary components for The Bug Genie, and you can continue to the actual
upgrade as soon as it is completed.

The document root folder has been renamed from thebuggenie/ to public/ - if you
are using a virtual host setup, remember to update the configuration to point
the document root to the public/ subfolder, instead of thebuggenie/


upgrade via web
---------------

Before starting the upgrade, please check the following:
* Make sure the "installed" file contains the correct main version (3.2 for any 3.2.x versions, etc) of your setup (you can find this by visiting <thebuggenie.url>/about)
* Make a backup of your database
* If using file storage, also make a backup of your files/ folder

Also, when upgrading from 3.2, make sure you follow these steps before starting the actual upgrade:
* Remove the folder /core/cache entirely, including subfolders and/or files
* Create a folder named "cache" under the root thebuggenie/ folder
* Copy the file core/config/b2db.sample.yml -> core/config/b2db.yml, and use the values from core/b2db_bootstrap.inc.php to populate the file you copied
* Create a file called "upgrade" in the directory where "installed" is located
* Make both "installed" and "upgrade" writable by the web-server
* Update any links in your web server configuration from pointing to the thebuggenie/ subfolder, to point to the public/ subfolder

Then, visit the location <thebuggenie.url>/upgrade in your web-browser and the
upgrade wizard will start, and guide you through the upgrade process.

The version number in the "installed" file will be automatically updated

via command-line
----------------

Command-line upgrades are not supported.