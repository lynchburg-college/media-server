# Lynchburg College Media Server

Clone this project into /var/www


Prerequisites:

* apache2             : Web server
* avconv              : AV Conversion  tool, formerly FFMpeg (already included in most distros)
* libavcodec-extra-53 : Conversion codecs
* mysql               : Database server for metadata
* php5                : Web scripting
* php5-mysql          : PHP library for mySQL connection
* php5-ldap           : LDAP library for authentication
* phpmyadmin          : Web tool for administering mySQL
* ffmpegthumbnailer   : Creates nice little thumbnails

Command:
sudo apt-get install apache2 mysql-server mysql-client php5 php5-ldap php5-mysql phpmyadmin ffmpegthumbnailer libavcodec-extra-53 



Setup:


Database Setup:

    Connect to http://localhost/phpmyadmin as root 

    * Create a new user (any name will do, `media` or something like that is good) 
    * -- Privileges > Add New User
    * -- User Name : whatever
    * -- Host      : local
    * -- Password  : whatever
    * -- Create database with the same name and grant all

    * Move into the database you just created
    * -- Click Import > browse to database.sql and load it in.  
   


Copy config.default to config.php, then edit in your own settings (database user/name from step 1) 


Create the /content directory structure.  Optionally, you can mount to anywhere you like, so long as it follows this structure:
You need to create each of these directories, and give the whole tree read+write to all 

    (from the root directory, probably /var/www/media-server)

    mkdir ./content
    mkdir -p ./content/available
    mkdir -p ./content/incoming
    mkdir -p ./content/converting
    mkdir -p ./content/log
        
    chmod -R 0777 ./content

    
Modify the PHP ini file ( /etc/php5/apache2/php.ini )
Note - root path is probably /var/www/media-server
    
    upload_tmp_dir=<root path>/content/incoming   
    upload_max_filesize=3G
    max_execution_time = 0
    max_input_time = -1
    memory_limit = 4G
    error_log = <root path>/error.log
    post_max_size = 5G
    


Usage:
    To upload :  First, log in.  Then, drag / drop video file onto the main page.


    


