Onboard - Ansible
======================

The included ansible playbook and role install the onboard web application along with required dependencies.

These files also serve as living documentation of the system requirements and configurations necessary to run the application.

This assume some familiarity with the Ansible configuration management system and that you have an ansible control machine configured. Detailed instructions for getting up and running on Ansible are maintained as part of our example-playbooks repository:

https://github.com/City-of-Bloomington/example-playbooks

On the ansible control machine, make sure you have everything you need:

    git clone https://github.com/City-of-Bloomington/onboard
    cd onboard/ansible

Variables
--------------
### Installation paths
The archive path is the path to the tarball you downloaded.  If you cloned from Github, you'll need to do a build to create the release archive.

```YAML
onboard_archive_path: "../build/onboard.tar.gz"
onboard_install_path: "/srv/sites/onboard"
onboard_backup_path:  "/srv/backups/onboard"
```

### Apache configuration
The max image size is the largest upload file size accepted.  Users will not be able to upload files larger than this size.
```YAML
onboard_base_uri: "/onboard"
onboard_base_url: "https://{{ ansible_host }}{{ onboard_base_uri }}"
onboard_max_image_size: "100M"
```

### Database
You will want to vault the password

```YAML
onboard_db:
  host:     "localhost"
  name:     "onboard"
  username: "onboard"
  password: "{{ vault_onboard_db.password }}"
```

### Google Calendar Integration
OnBoard uses Google Calendar as the master source of meeting event information.  Each board has it's own calendar.  OnBoard does server to server communication with Google, using the Google API PHP client.  You will need to register for a domain-wide service account with Google.  You can do this at the Google Developers Console.

https://console.developers.google.com

Instructions on how to register for a domain-wide service account are here:
https://developers.google.com/api-client-library/php/auth/service-accounts

```YAML
onboard_google:
  calendar: "https://calendar.google.com/a/bloomington.in.gov"
  user:     "user@gmail.com"
  credentials: |
```

The calendar variable is a base url for displaying the various Google Calendars used by the boards.
The user variable is the email address of the user who will have permission to edit events on these calendars.
The credentials variable is the contents of the service account credentials.json file.

### ReCaptcha support
OnBoard is written to use ReCaptcha to prevent spam during the public board application process.  You will need to register with ReCaptcha and set the site_key and server_key provided to you.  You probably want to vault these.
```YAML
onboard_recaptcha:
  site_key:   "{{ vault_onboard_recaptcha.site_key   }}"
  server_key: "{{ vault_onboard_recaptcha.server_key }}"
```

Dependencies
-------------

Decide how you want to get the other necessary ansible roles:

    ansible-galaxy install -r roles.yml

or for development:

```bash
git clone https://github.com/City-of-Bloomington/ansible-role-linux.git ./roles/City-of-Bloomington.linux
git clone https://github.com/City-of-Bloomington/ansible-role-apache.git ./roles/City-of-Bloomington.apache
git clone https://github.com/City-of-Bloomington/ansible-role-apache.git ./roles/City-of-Bloomington.mysql
git clone https://github.com/City-of-Bloomington/ansible-role-php.git ./roles/City-of-Bloomington.php
```



Run the Playbook
-----------------

    ansible-playbook deploy.yml -i ./inventory

Additional Information
-------------------------
Did everything work as expected? If not, please let us know:

https://github.com/City-of-Bloomington/onboard/issues

This project and others like it are maintained on the City of Bloomington's Github page:

https://github.com/City-of-Bloomington

License
-------

Copyright (c) 2016-2017 City of Bloomington, Indiana

This material is avialable under the GNU General Public License (GLP) v3.0:
https://www.gnu.org/licenses/gpl.txt
