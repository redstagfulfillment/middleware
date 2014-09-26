Middleware
========
Middleware is an abstracted and lightweight version of Red Stag Fulfillment's production environment. It can be used for
developing plugins for the production environment, or as a standalone "middle-man" between your systems and ours.


Requirements
--------

* The supported platform for developing Middleware plugins is PHP 5.4+ on Mac or Linux.
* A web server is not required as Middleware plugins are only run from the command line or a crontab.
* [modman](https://github.com/colinmollenhour/modman) is used to deploy plugins.


Installation
--------
1. Clone this repository to a new directory and copy the sample config file.<br/>

    `$ git clone https://github.com/redstagfulfillment/middleware.git`<br/>
    `$ cd middleware`<br/>
    `$ modman init`<br/>
    `$ cp app/etc/local.sample.xml app/etc/local.xml`

2. Edit app/etc/local.xml file and add your configuration.<br/>
Example:<br/>

    `<base_url>http://[WEBSITE BASE URL]/api/jsonrpc</base_url>`<br/>
    `<login>[API LOGIN]</login>`<br/>
    `<password>[API PASSWORD]</password>`<br/>

3. Clone the `Test_Test` plugin and run the `update_ip` method to confirm successful setup:<br/>

    `$ modman clone https://github.com/redstagfulfillment/plugin-test.git`<br/>
    `$ php run.php Test_Test update_ip`

#### Windows Users

Although Windows is not supported, it should be possible to develop in Windows by using a [PHP port of modman](https://github.com/sitewards/modman-php)
or by manually creating the symlinks for a module. However, if you want your plugin to be installed on the production environment
it must operate perfectly under a Linux environment.


Running Plugins
--------

Plugins can be run by executing the following command in the command line:<br/>

`php run.php {COMPANY_NAME}_{MODULE_NAME} {PLUGIN_METHOD}`


Create Your Own Plugin
--------
The easiest way to start your own plugin is to fork the [`Test_Test`](https://github.com/redstagfulfillment/plugin-test)
plugin and rename it. The minimal required file structure from the root of the middleware directory is as follows:<br/>

* app/code/community/{COMPANY_NAME}/{MODULE_NAME}
  * etc
    * config.xml
    * manifest.xml
  * Plugin.php

As such, the `modman` file for the `Test_Test` plugin looks like this:

```
code             app/code/community/Test/Test/
Test_Test.xml    app/etc/modules/
```
