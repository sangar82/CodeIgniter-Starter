###################
Codeigniter Starter 2.1
###################

Custom Codeigniter with preinstalled features:

- Sparks installed (http://getsparks.org/)
- Sangar Auth  (Ion-Auth modified with works with PHP-activerecord) and ready to work (all views translateds and CRUD users)
- Php-ActiveRecord installed (https://github.com/kla/php-activerecord) (examples of validations, relations, callbacks) Version Nightly build May 2012
- Subdomains for languages
- Controllers & Methods names with translations
- Backend Template
- Example: Categories as a tree of categories (with order via ajax)
- Example of code (CRUD users, CRUD categories, CRUD products with upload and thumbnail)
- Translation on 3 languages (english, spanish, catalan)
- Basic layout (frontend, backend)
- System Messages implemented as partials in layouts
- Folder for public content (images, js, uploads, ...)
- Includes basic before_filter and after_filter support (Matthew Machuga) https://github.com/machuga/codeigniter-filter
- Toast Unit test (Sangar Auth test, categories test, products test)
- Ignited Scaffolding: a new way to do a scoffolding.

Ready to work!


*******************
Server Requirements
*******************

-  PHP version 5.3.5 or newer.


************
Installation
************

Download and copy to your document root folder

Create the database with the mysql_dump.sql file

Edit the config files:

- config.php
- database.php
- sangar_auth.php

Create a virtualhost for to access via www.yourdomain.com (the same that you wrote in config.php)

Enjoy!!


************
Backend user and password
************

The default user to access to the private zone is:

    user: 		admin@admin.com

    password: 	password


************
Translate controllers name and controllers method names
************

If you want to translate controllers names and controllers method names edit the file 

	/application/language/controller_translations.php

and 

	/application/language/method_translations.php. 


For use in your views use the helper transurl_helper.php

::

	<?=lang_anchor(controller_name, method_name, params)?>

Example
::

	<?=lang_anchor('users', 'edit', 1)?>

(It takes the actual language and creates an anchor based on the local URL with translations of method name and controller name)

