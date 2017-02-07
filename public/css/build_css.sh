#!/bin/bash
echo "Compiling core CSS"
node-sass --output-style compact --source-map ./ screen.scss ./screen.css
