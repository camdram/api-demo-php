Camdram API PHP Demo
===============================

A very basic demo of OAuth authentication with Camdram plus accessing authorised shows.

A live demo can be viewed at http://camdram-api-demo.herokuapp.com

A host-specific API key is required to run the demo, which can be created at https://www.camdram.net/api/apps

Copy '.env' to '.env.local' and enter the generated API key and API secret. Run the following commands to launch the demo: 

    curl -sS https://getcomposer.org/installer | php
    ./composer.phar install
    ./bin/console server:start
