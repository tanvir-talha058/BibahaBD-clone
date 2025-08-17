@echo off
echo ===================================================
echo MySQL Root Password Reset Tool for XAMPP
echo ===================================================
echo.
echo This batch file will:
echo 1. Stop MySQL service
echo 2. Start MySQL with --skip-grant-tables option
echo 3. Reset root password to blank (empty)
echo 4. Restart MySQL normally
echo.
echo Make sure you're running this as Administrator!
echo.
pause

echo.
echo Stopping MySQL service...
net stop mysql 2>nul
taskkill /f /im mysqld.exe 2>nul
echo.
echo Starting MySQL with skip-grant-tables option...
echo This will open a new console window, DO NOT CLOSE IT until the process is complete
start "MySQL Skip Grant" /min cmd /c "cd /d C:\xampp\mysql\bin && mysqld.exe --skip-grant-tables --skip-networking"

echo.
echo Waiting for MySQL to start...
timeout /t 5 /nobreak > nul

echo.
echo Resetting root password...
cd /d C:\xampp\mysql\bin
echo USE mysql; > reset_pwd.sql
echo UPDATE user SET authentication_string='' WHERE User='root'; >> reset_pwd.sql
echo UPDATE user SET plugin='mysql_native_password' WHERE User='root'; >> reset_pwd.sql
echo FLUSH PRIVILEGES; >> reset_pwd.sql
echo EXIT; >> reset_pwd.sql

mysql -u root < reset_pwd.sql
del reset_pwd.sql

echo.
echo Stopping temporary MySQL instance...
taskkill /f /im mysqld.exe
timeout /t 2 /nobreak > nul

echo.
echo Starting MySQL service normally...
net start mysql 2>nul || echo Failed to start MySQL service - try starting it from XAMPP control panel

echo.
echo ===================================================
echo Password reset process complete!
echo.
echo Your MySQL root password is now blank (empty string)
echo Make sure your PHP files use:
echo $password = "";
echo.
echo Now test your connection with test_mysql_connection.php
echo ===================================================
echo.
pause
