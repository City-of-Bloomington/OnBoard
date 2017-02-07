#!/bin/bash
echo "Compiling theme CSS"
node-sass --output-style compact --source-map ./ screen.scss ./screen.css
