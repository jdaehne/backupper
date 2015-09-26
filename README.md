# MODX Backupper
MODX backup script to backup database and all the files in one click.


Inspired by the backup script of Oliver Haase-Lobinger (http://de.modx.com/blog/2014/03/modx-revolution-updates-vereinfachen) i just added a basic UI to make the backup more comfortable.

## Backup MODX:
Place the backup.php file in the same directory as your root. Load it in your browser, select the elements you want to backup and name your backup directory. The script will create the folder if it does not exist. The script will not include that directory in the backup.

You can just rightclick and download the files after your backup is done. Now you can click the "remove-button" to delete the script of your server.


## Update MODX:
If you also want to update your MODX version and already placed the install.php file from sottwell (https://github.com/sottwell/installer) in the same directory, the script will show a button "remove & update" to remove the backup.php file and redirect you to the update file.


## Extract Archiv:
If you move your Backup (tar-file) to a new Server, just place the tar-file and the backup.php script into the root and call backup.php in your browser. The script will find the tar-files and you can extract the archive.


### Backup by crontab:
If you want to schedule your backup via crontab just call the script with the get values like: modx-backup.php?mysql=1&files=1&folder=backup&cron=1


### Environment:
I hope this script will be helpfull for you. It works on my webhosting environment, but may needs some changes on others. Let me know.
