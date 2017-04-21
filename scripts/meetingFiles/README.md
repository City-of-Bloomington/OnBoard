Ingesting Meeting Files
-----------------------

The ingest scripts can be used to bring in large numbers of meeting files.

## Moved not Copied
The ingested files will be MOVED into place.  This means your directories of files to ingest will be emptied out files when the process is complete.  You probably want to prepare for ingesting by creating copies of all the files you want to ingest.

This will probably happen naturally, as the script has to run on the web server.  Because of that, you will end up needing to copy your directory of files across to the web server, anyway.  Just remember, that directory will be emptied of files as the ingest script runs.

### Prep the Files
While creating the file copies to ingest you might want to run through and make sure the filenames match the data.  Typically, users have used the meeting date in the filename; however, we find ~5% of the time, the date in the filename does not match the date in the contents of the file.

The ingest script uses data from a CSV file, not from the files themselves.  Once you've got all your files copied into a temp location, use the "prepareSpreadsheet.php" script to generate a CSV file with all the filenames.  You will then need to run through and type in the actual meeting date for each file.

You will need to create a separate CSV file for each type (Agenda, Minutes, Packet) you will be ingesting.


### Run the ingest script
This will MOVE the files into place


## Troubleshooting

### File Permissions
The meeting files and data directory are owned by www-data.  If you are running this script as yourself, you might run into permission problems.  Either make sure permissions are set appropriately in SITE_HOME/data or run the script as www-data.

Another place permission problems come up is with the PDF generation.  OnBoard will try to generate PDF versions of files that are ingested.  This is done using OpenOffice (LibreOffice).  Running soffice in headless mode still generates a config directory for LibreOffice.  This config directory is typically created by www-data.  If you're running the script as yourself, you'll need to make sure you have permission to read/write SITE_HOME/data/.config (and all subdirectories).
