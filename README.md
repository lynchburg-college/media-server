# Lynchburg College Media Server

Prerequisites:

avconv (apt-get install avconv)

mysql (apt-get install mysql)



Setup:

1.  Create a new mySQL database using the database.sql script

2.  Examine config.default, plug in your own settings, then save as config.php


3.  The /content directory can mount to anywhere you like, so long as it follows this structure:

    /content
    /content/available
    /content/incoming
    /content/converting



