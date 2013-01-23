
#Codeigniter Starter 

Custom Codeigniter with preinstalled features:

- Sparks installed (http://getsparks.org/)
- Sangar Auth Library. (Ion-Auth modified for me working under PHP-activerecord)
- Sangar Scaffolding Library. A new way to do scaffolding!
- Php-ActiveRecord installed (https://github.com/kla/php-activerecord) Version Nightly build May 2012
- Backend & Full Access Control (login, logout, remember password, and protected access to the backend)
- Template Library by Phil Sturgeon implemented (http://philsturgeon.co.uk/demos/codeigniter-template/user_guide/)
- Basic layout (frontend, backend)
- Toast Unit test (http://jensroland.com/projects/toast/)
- Includes basic before_filter and after_filter support (Matthew Machuga) https://github.com/machuga/codeigniter-filter
- System Messages implemented as a partial in layouts
- Folder for public content (images, js, uploads, ...)
- Subdomains for multi-language
- Translation on 3 languages (english, spanish, catalan)
- Methods for translate controllers and methods names on URL
- CRUD Users implemented.  
- Example: CRUD Categories. Categories as a tree of categories (with order via ajax)
- Example: CRUD Products with upload an image  and thumbnail creation
- Examples of testing (SangarAuth Test, users test, categories test, products test).


##Codeigniter Version

The Codeigniter Version is 2.1.3 


##Server Requirements

PHP version 5.3.5 or newer.
At this moment, Scaffolding works only with MySql and models works with phpactiverecord


##Installation

The installation is the same as Codeigniter.

Please see the installation instruccions of Codeigniter <http://codeigniter.com/user_guide/installation/index.html>

Create the database and import the tables with the mysql_dump.sql file

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


##How create a new scaffold

Sangar Scaffolds creates the files for CRUD operations for you!

It creates the tables on the database, the controllers, the models and the views.

It also modifies the routes.php file.

You can create forms with the followings elements:

- name
- textarea
- radiobuttons
- checkboxes
- select
- select 1:N (populate the form select with a existent Model)
- upload images (with thumbnail creation and uploads rules)
- upload files (with uploads rules)
- hidden relational (It's a special element. Only one hidden relational by scaffolding is allowed. It will produce a form with relation 1:N linked with his parent form automatically)

This version has more features that sangar scaffold spark. http://getsparks.org/packages/sangar-scaffold/versions/HEAD/show

Each element has validation rules and the possibility to do it multilanguage.

Create also a paginated list view.


To create a new scaffold, login into the private zone, and search the link 'Scaffolds' on the top of the page

- Write the Controller name you want produce.
- Write the Model name you want produce.
- Copy the code blocks of elements you need and paste to scaffold code textarea. Each code block must be separated by commas. The scaffold code is a JSON without the first '{' and the last '}'
- Choose the options you want
- Scaffold!



##Translate controllers name and controllers method names

If you want to translate controllers names and controllers method names edit the file 

	/application/language/controller_translations.php

and 

	/application/language/method_translations.php. 


For use in your views use the helper transurl_helper.php:

	<?=lang_anchor(controller_name, method_name, params)?>

Example:

	<?=lang_anchor('users', 'edit', '1')?>

(It produces a link in the actual language, with the controller and method names translated)

