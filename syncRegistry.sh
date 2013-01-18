/bin/bash

# run this via cronjob

# Adjust folder names
#SOURCE_FILE1=/home/.../wpnxm/updater/wpnxm-software-registry.php
#TARGET_FILE2=/var/www/.../wpnxm/wpnxm-software-registry.php

if [ $SOURCE_FILE1 -nt $TARGET_FILE2 ]; then
  #echo "File 1 is newer than file 2"
  cp -u $SOURCE_FILE1 $TARGET_FILE2
else
  #echo "File 1 is older than file 2"
   echo "No registry update necessary. The repository file is older than the file in the webfolder."
fi