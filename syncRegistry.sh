#!/bin/bash

# This shell script is executed via cronjob.

# YOU MIGHT NOT FORGET TO ADJUST THE FOLDER NAMES

#
# -- 1 -- It copies the latest wpnxm-software-registry.php
#         from the git repository path to the webserved path.
#

SOURCE_FILE1=/home/git-repos/wpnxm/updater/wpnxm-software-registry.php
TARGET_FILE1=/var/www/{username}/wpnxm/wpnxm-software-registry.php

# compare the modification date/time of files
if [ $SOURCE_FILE1 -nt $TARGET_FILE1 ]; then
  #echo "Source File newer than Target File."
  cp -u $SOURCE_FILE1 $TARGET_FILE1
else
  #echo "Source File older than Target File."
   echo "No registry update necessary. The repository file is older than the file in the webfolder."
fi

#
# -- 2 -- It copies the latest wpnxm-software-registry-{versionized}.csv files
#         from the git repository path to the webserved path.
#

# CSV files
CSV_FILES="/home/git-repos/wpnxm/updater/wpnxm-software-registry-*.csv"

for f in $CSV_FILES
do
	FILENAME="${f##*/}"
    TARGET_FILE="/var/www/{username}/wpnxm/{$FILENAME}.csv"

	if [ "$f" -nt $TARGET_FILE ]; then
	  echo "Source File {$f} newer than Target File {$TARGET_FILE}."
	  #cp -u  "$f" $TARGET_FILE1
	else
	  echo "Source File {$f} older than Target File {$TARGET_FILE}."
	fi
done