# The Bug Genie

[![Build Status](https://travis-ci.org/thebuggenie/thebuggenie.png?branch=master)](https://travis-ci.org/thebuggenie/thebuggenie)

The Bug Genie is an enterprise-grade issue-tracking, development and project
management system. Main features includes:
* Integrated wiki
* Live project planning
* Great agile project support
* Customizable workflow support
* Time tracking
* Multiple hosted installations on single setups
* Complete source code integration
* Command-line interface for both local and remote installations
* LDAP authentication, OpenID enabled logins and pluggable auth backend
* Remote API (JSON-based)
* Great web-based configuration
* Module-based and extensible architecture
* Integration with source-code control systems

... and a lot more!

For up-to-date installation and setup notes, visit the FAQ:
http://issues.thebuggenie.com/wiki/TheBugGenie:FAQ


## Installation

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


### 3: Install via web

Visit the subfolder `/thebuggenie/index.php` in your web-browser.

The installation script will start automatically and guide you through the
installation process.


### 4: Install via command-line (unix/linux only)

You can use the included command-line client to install, if you prefer that.
Run `./tbg_cli` from this folder.

To install:
`./tbg_cli install`


## REPORTING ISSUES

If you find any issues, please report them in the issue tracker on our website:
http://issues.thebuggenie.com


## RUNNING PHPUNIT TESTS

By executing `composer.phar install --dev` during the installation process, phpunit 4.2 will get installed. The phpunit test can be run by the following command:
```
vendor/bin/phpunit
```
