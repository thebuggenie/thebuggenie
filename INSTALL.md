INSTALLATION
============

NOTE: For the latest installation notes, troubleshooting and tips,
visit the FAQ: http://issues.thebuggenie.com/wiki/TheBugGenie:FAQ

GENERAL INSTALLATION NOTES
--------------------------

### 1: Download and install Composer

The Bug Genie uses a dependency resolution tool called Composer, which must
be downloaded and run before The Bug Genie can be installed or used.

Download and install Composer from http://getcomposer.org


### 2: Install The Bug Genie dependencies

After you have followed the instructions in step 1, run
`php composer.phar install`
from the main directory of The Bug Genie. Composer will download and install
all necessary components for The Bug Genie, and you can continue to the actual
installation as soon as it is completed.


### 3: (recommended) Set up a web server virtual host

If you have access to a web server setup, configure a separate virtual host for
The Bug Genie, with the document root pointing to the public/ subfolder of the 
main thebuggenie/ directory.

If you are using Apache, enable url rewriting (the installation routine will 
help you complete the url rewriting setup), and if you're using IIS, Nginx or
others, look at the examples included in the online documentation at
http://issues.thebuggenie.com/wiki/TheBugGenie:FAQ


### 4: Install via web

Visit the subfolder `/public/index.php` in your web-browser, or point your web
browser to the virtual host domain you set up in step 3.

The installation script will start automatically and guide you through the
installation process.


### 4: Install via command-line (unix/linux only)

You can use the included command-line client to install, if you prefer that.
Run `./tbg_cli` from this folder.

To install:
`./tbg_cli install`
