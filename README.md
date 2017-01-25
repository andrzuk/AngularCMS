# AngularCMS

## Simple CMS based on AngularJS frontend framework

### Application features:
* users authentication (login, register)
* resources access levels (general roles and particular users access)
* admin panel (settings, templates, styles, scripts, menu, articles, gallery)
* activity reports (messages, searches, visits)

### General:
* Language: JavaScript, jQuery
* Framework: AngularJS
* API services: PHP
* Database: MySQL
* User Interface: Bootstrap 3
* On-line: http://angular-cms.pl

### Installation:
* create new empty database on hosting account
* unpack ZIP and upload to hosting server to public_html folder
* write database connection parameters in app/db/connection.php
* open web page in the browser - URL: http://{your-domain}/
* fill settings form and submit
* that's all!

### Reset application:
* upload /install/install_database.php to hosting server to public_html/install folder
* open web page in the browser - URL: http://{your-domain}/
* fill settings form and submit
* that's all!

### Note:
* Make sure to install to public_html/ root folder - not to any subfolder, so that "base_href" was "/", not "/subfolder/" !!!
