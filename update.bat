@echo off
echo Pulling latest changes for this repository...
echo.
git pull
echo.
echo Pulling the the latest changes for all submodules:
git submodule init
git submodule foreach git pull origin master
echo.
echo  Submodule Status 
echo  ================
echo.
git submodule status
echo.
pause