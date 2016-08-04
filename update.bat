@echo off
echo Pulling latest changes for this repository...
echo.
git pull
echo.
echo Pulling latest changes for all submodules...
git submodule init
git submodule update
echo.
echo  Submodule Status 
echo  ================
echo.
git submodule status
echo.
pause