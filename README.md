
#Codeigniter Starter 

Custom Codeigniter with preinstalled features:

- Sparks installed (http://getsparks.org/)
- Sangar Auth Library. (Ion-Auth modified for me working under PHP-activerecord)
- Sangar Scaffolding Library. A new way to do scaffolding!
- Php-ActiveRecord installed (https://github.com/kla/php-activerecord) Version Nightly build May 2012
- Toast Unit test 
- Includes basic before_filter and after_filter support (Matthew Machuga) https://github.com/machuga/codeigniter-filter


- Backend & Full Access Control (login, logout, remember password, and protected access to the backend)
- Basic layout (frontend, backend)
- Backend Template


- System Messages implemented as partials in layouts
- Folder for public content (images, js, uploads, ...)


- Subdomains for languages
- Translation on 3 languages (english, spanish, catalan)
- Methods for translate controllers and methods names 


- CRUD Users implemented.  
- Example: CRUD Categories. Categories as a tree of categories (with order via ajax)
- Example: CRUD Products with upload an image and make a thumbnail
- Examples of testing (SangarAuth Test, users test, categories test, products test).


Under development: the first full operative version in a few weeks.



##Codeigniter Version

The Codeigniter Version is 2.1.2 


##Server Requirements

PHP version 5.3.5 or newer.



##Installation

The installation is the same as Codeigniter.

Please see the installation instruccions of Codeigniter <http://codeigniter.com/user_guide/installation/index.html>

Create the database with the mysql_dump.sql file

Edit the files with your preferences (domain, languages, database, authentification):

- config.php
- database.php
- sangar_auth.php

Create a virtualhost with the the same domain that you write in config.php.
If you want more languages, create more ServerAlias

	<VirtualHost *:80>

		ServerName www.mydomain.com

		ServerAlias en.mydomain.com

		ServerAlias es.mydomain.com

		ServerAlias ca.mydomain.com

		DocumentRoot /www/mydomain.com
	
	</VirtualHost>

Enjoy!!



##Backend user and password

The default user to access to the private zone is:

    user: 		admin@admin.com

    password: 	password



##Translate controllers name and controllers method names

If you want to translate controllers names and controllers method names edit the file 

	/application/language/controller_translations.php

and 

	/application/language/method_translations.php. 


For use in your views use the helper transurl_helper.php

::

	<?=lang_anchor(controller_name, method_name, params)?>

Example
::

	<?=lang_anchor('users', 'edit', '1')?>

(It takes the actual language and creates an anchor based on the local URL with translations of method name and controller name)

