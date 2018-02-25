Camdram API PHP Demo
===============================

A very basic demo of OAuth authentication with Camdram plus accessing authorised shows.

A host-specific API key is required for the demo, which can be created at https://www.camdram.net/api/apps

Copy 'config.sample.php' to 'config.php' and enter the API key and API secret in the appropriate places.  

    curl -sS https://getcomposer.org/installer | php
    ./composer.phar install
    ./runserver
