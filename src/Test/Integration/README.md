# Integration Tests
These tests make sure that the system has been installed and configured
correctly and is working as expected.  As such, they do, in fact, hit
the production database and write files to the production file locations.

The tests do not write to the production database, only read.
The tests do write files into SITE_HOME, but should clean up after themselves.
