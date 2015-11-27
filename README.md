# MODX Backupper
MODX backup script to backup database and all the files in one click.


Inspired by the backup script of Oliver Haase-Lobinger (http://de.modx.com/blog/2014/03/modx-revolution-updates-vereinfachen) i just added a basic UI to make the backup more comfortable.

## Backup MODX:
Place the backup.php file in the same directory as your root. Load it in your browser, select the elements you want to backup and name your backup directory. The script will create the folder if it does not exist. The script will not include that directory and itself in the backup.

You can just rightclick and download the files after your backup is done. Either MySQL, Files or both combined as one archive. After downloading you can click one of the "remove-buttons" to delete either the script or the script and the backup-folder of your server. I recommend last, to make shure that nobody can download the backupfiles with your config in it.


## Extract Archiv:
If you move your backup (tar-file) to a new server, just place the tar-file and the script into the root and call backup.php in your browser. The script will find the tar-files and asks you to extract them.


### Backup by crontab:
If you want to schedule your backup via crontab just call the script with the get values like: backup.php?mysql=1&files=1&folder=backup&cron=1


### Environment:
I hope this script will be helpfull for you. It works on my webhosting environment, but may needs some changes on others. Let me know.
