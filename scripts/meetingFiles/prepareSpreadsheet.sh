#!/bin/bash

# The full path to a directory of files to import
# The files should all be of the same type (Agenda, Minutes, Packet)
DIR=`pwd`/Agendas
CSV=./agendas.txt

find $DIR -type f -not -path '*/\.*' > $CSV
