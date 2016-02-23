OnBoard
=================

An extensible web application to keep track of boards & commissions details, the people appointed to those groups, any legislation they write, and the voting records of each committee member.

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