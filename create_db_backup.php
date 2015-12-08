<?php
/*
This script backups your MySQL databases and optionally transfer them to one or more ftp server.


The MIT License (MIT)

Copyright (c) 2015 www.usolved.net 

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/



//----------------------------------------------------------------
//Set defaults
//----------------------------------------------------------------

set_time_limit(0);
$db_count 						= -1;
$ftp_count 						= -1;


//----------------------------------------------------------------
//DB credentials
//----------------------------------------------------------------
//Configure credentials of one or more database to backup

//DB 1
$db_count++;
$db[$db_count]['db_user'] 		= "";
$db[$db_count]['db_password'] 	= "";
$db[$db_count]['db_name'] 		= "";
$db[$db_count]['sql_file'] 		= "dump_".date('Y-m-d')."_{$db[$db_count]['db_name']}.sql";


//DB 2
/*
$db_count++;
$db[$db_count]['db_user'] 		= "";
$db[$db_count]['db_password'] 	= "";
$db[$db_count]['db_name'] 		= "";
$db[$db_count]['sql_file'] 		= "dump_".date('Y-m-d')."_{$db[$db_count]['db_name']}.sql";
*/


//----------------------------------------------------------------
//FTP credentials
//----------------------------------------------------------------
//Configure credentials of one or more ftp server to transfer the backup

//FTP 1
$ftp_count++;
$ftp[$ftp_count]['ftps'] 				= false;
$ftp[$ftp_count]['ftp_server'] 			= "";
$ftp[$ftp_count]['ftp_user'] 			= "";
$ftp[$ftp_count]['ftp_password'] 		= "";
$ftp[$ftp_count]['ftp_passive_mode'] 	= true;
$ftp[$ftp_count]['ftp_remote_folder'] 	= "";	//e.g. /mysite/backups


//FTP 2
/*
$ftp_count++;
$ftp[$ftp_count]['ftps'] 				= false;
$ftp[$ftp_count]['ftp_server'] 			= "";
$ftp[$ftp_count]['ftp_user'] 			= "";
$ftp[$ftp_count]['ftp_password'] 		= "";
$ftp[$ftp_count]['ftp_passive_mode'] 	= true;
$ftp[$ftp_count]['ftp_remote_folder'] 	= "";
*/


//----------------------------------------------------------------
//Interate over all databases
//----------------------------------------------------------------

foreach($db as $db_item)
{
	//Create SQL dump and gzip the dumped file
	exec("mysqldump -u {$db_item['db_user']} -p{$db_item['db_password']} --allow-keywords --add-drop-table --complete-insert --hex-blob --quote-names {$db_item['db_name']} > {$db_item['sql_file']}");
	exec("gzip {$db_item['sql_file']}");


	//----------------------------------------------------------------
	//FTP transfer: Transfer sql dump to the configured ftp servers
	//----------------------------------------------------------------

	if($ftp_count >= 0)
	{
		foreach($ftp as $ftp_item)
		{
			//Initiate connection
			if($ftp_item['ftps'])
				$connection_id = ftp_ssl_connect($ftp_item['ftp_server']);
			else
				$connection_id = ftp_connect($ftp_item['ftp_server']);

			if(!$connection_id)
				echo "Error: Can't connect to {$ftp_item['ftp_server']}\n";


			//Login with user and password
			$login_result = ftp_login($connection_id, $ftp_item['ftp_user'], $ftp_item['ftp_password']);

			if(!$login_result)
				echo "Error: Login wrong for {$ftp_item['ftp_server']}\n";


			//Passive mode?
			ftp_pasv($connection_id, $ftp_item['ftp_passive_mode']);

			//Upload file to ftp
			if (!ftp_put($connection_id, $ftp_item['ftp_remote_folder']."/".$db_item['sql_file'].'.gz', $db_item['sql_file'].'.gz', FTP_BINARY))
			{
				echo "Error: While uploading {$db_item['sql_file']}.gz to {$ftp_item['ftp_server']}.\n";
			}

			//Close ftp connection
			ftp_close($connection_id);
		}
	}

	//Delete original *.sql file
	if(file_exists($db_item['sql_file']))
		unlink($db_item['sql_file']);
}


?>