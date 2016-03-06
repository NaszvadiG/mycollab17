@echo.
@echo off

SET lib=%~dp0
php -q "%lib%run.php" %*

echo.