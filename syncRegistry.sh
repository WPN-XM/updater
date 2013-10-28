/bin/bash

# run this via cronjob

# Adjust folder names
#SOURCE_FILE1=/home/.../wpnxm/updater/wpnxm-software-registry.php
#TARGET_FILE1=/var/www/.../wpnxm/wpnxm-software-registry.php

if [ $SOURCE_FILE1 -nt $TARGET_FILE1 ]; then
  #echo "Source File newer than Target File."
  cp -u $SOURCE_FILE1 $TARGET_FILE1
else
  #echo "Source File older than Target File."
   echo "No registry update necessary. The repository file is older than the file in the webfolder."
fi