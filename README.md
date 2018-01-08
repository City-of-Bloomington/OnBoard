OnBoard
=================

An extensible web application to keep track of boards & commissions details, the people appointed to those groups, any legislation they write, and the voting records of each committee member.

The City of Bloomington uses this application to track information about our boards & commissions. To get a feel for this system, you can view our instance here:

https://bloomington.in.gov/onboard

OnBoard is open source and released under the terms of the GNU Affero Public License.


## Installation

We use the same configuration for our PHP applications. To make sure the documentation stays up to date, we maintain it separately. It is available here:

https://github.com/City-of-Bloomington/blossom/wiki

### Additional Requirements

This application also uses LibreOffice to convert files to PDF.  You will need to install the headless version of LibreOffice Writer.  For Ubuntu systems this is

```bash
apt-get install libreoffice-common libreoffice-writer
```

Make sure Apache has permission to write into the SITE_HOME directory.  With some older versions of LibreOffice, I also had to give Apache ownership of that directory.

## Drupal

We also have a drupal module for pulling data from our OnBoard service and integrating it with other content about boards and commissions that is part of our public website. This drupal module is also available on GitHub here:

https://github.com/City-of-Bloomington/drupal-module-onboard
