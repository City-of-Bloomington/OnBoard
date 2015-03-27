These files are for customizations to a default Alfresco installation.  We want to do this in such a way that we can upgrade alfresco, over time, by simply replacing the WAR files.

In order to achieve this, we must make sure to place all customizations in the /shared/classes directory.

The official documentation always talks about modifying files directly in the webapps. So, the official documentation must be read with care, always translating the paths and filenames to the corresponding /share/classes files, which usually have slightly different filenames.
