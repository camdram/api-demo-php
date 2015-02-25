Camdram API Demos
===============================

A very basic demo of OAuth authentication with Camdram plus accessing authorised shows in both PHP and Python

A host-specific API key is required for the dmeo, which can be created at https://www.camdram.net/api/apps

PHP
=============
    cd php

Copy 'config.sample.php' to 'config.php' and enter the API key and API secret in the appropriate places.  

    curl -sS https://getcomposer.org/installer | php
    ./composer.phar install
    ./runserver
  
Python
=============
    cd python
  
Copy 'config.sample.py' to 'config.py' and enter the API key and API secret in the appropriate places.

    python install -r requirements.txt
    python manage.py runserver
