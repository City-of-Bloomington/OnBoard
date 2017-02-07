Onboard - Ansible
======================

The included ansible playbook and role install the onboard web application along with required dependencies.

These files also serve as living documentation of the system requirements and configurations necessary to run the application.

This assume some familiarity with the Ansible configuration management system and that you have an ansible control machine configured. Detailed instructions for getting up and running on Ansible are maintained as part of our system-playbooks repository:

https://github.com/City-of-Bloomington/system-playbooks

On the ansible control machine, make sure you have everything you need:

    git clone https://github.com/City-of-Bloomington/onboard
    cd onboard/ansible

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

Variables
--------------

Vaulting (encrypting) any sensitive information is recommended.

More information about vaulting passwords is available here:

https://github.com/City-of-Bloomington/system-playbooks/tree/master/group_vars


Templates
--------------

It may be necessary to update the configuration file settings in the templates:

roles/onboard/templates/*

Run the Playbook
-----------------

    ansible-playbook playbooks/onboard.yml -i hosts.txt

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
