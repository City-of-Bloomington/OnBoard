#!/bin/bash
echo "Compiling core CSS"
pysassc -t compact -m screen.scss screen.css
