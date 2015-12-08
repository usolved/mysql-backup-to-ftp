# mysql-backup-to-ftp

## Overview

A simple PHP script to backup your MySQL/MariaDB database and optionally transfer it to one or more ftp server. You may want to do this this automatically by creating a cronjob that executes this script.

## Authors

Ricardo Klement ([www.usolved.net](http://usolved.net))

## Installation

Just upload the file create_db_backup.php to your server.
Make sure that the file isn't accessible by simply calling the URL.
So either protect the folder with htaccess or don't put it to your public folder.

Some shared webhosts don't allow the execution of the mysqldump or gzip command.
If that's the case you could try to rename the extension to *.phpx or run PHP in CGI mode for this file.

## Examples

### Define two databases for backup

```
//DB 1
$db_count++;
$db[$db_count]['db_user'] 		= "john";
$db[$db_count]['db_password'] 	= "secretpassword123";
$db[$db_count]['db_name'] 		= "customer";
$db[$db_count]['sql_file'] 		= "dump_".date('Y-m-d')."_{$db[$db_count]['db_name']}.sql";

//DB 2
$db_count++;
$db[$db_count]['db_user'] 		= "john";
$db[$db_count]['db_password'] 	= "secretpassword123";
$db[$db_count]['db_name'] 		= "products";
$db[$db_count]['sql_file'] 		= "dump_".date('Y-m-d')."_{$db[$db_count]['db_name']}.sql";
```

### Define ftp server for transfering the backups

```
//FTP 1
$ftp_count++;
$ftp[$ftp_count]['ftps'] 				= false;
$ftp[$ftp_count]['ftp_server'] 			= "ftp.myremotebackup.com";
$ftp[$ftp_count]['ftp_user'] 			= "john";
$ftp[$ftp_count]['ftp_password'] 		= "backuppassword123";
$ftp[$ftp_count]['ftp_passive_mode'] 	= true;
$ftp[$ftp_count]['ftp_remote_folder'] 	= "/db_backups";
```
