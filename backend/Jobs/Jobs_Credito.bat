@echo off
set param1=%1
set param2=%2

C:\xampp\php\php.exe -f .\controllers\JobsCredito.php -- %param1% %param2%