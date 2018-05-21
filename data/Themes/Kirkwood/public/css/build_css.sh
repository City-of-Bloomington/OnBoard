#!/bin/bash
echo "Compiling theme CSS"
pysassc -t compact -m screen.scss screen.css
