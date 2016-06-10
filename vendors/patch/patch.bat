@echo off
@rem Получаю абсолютный путь из относительного: http://stackoverflow.com/a/4488734/3551026
@set REL_PATH=..\..\web-server\vendor\php-5.3.28-Win32-VC9-x86
@set ABS_PATH=
@rem // save current directory and change to target directory
@pushd %REL_PATH%
@rem // save value of CD variable (current directory)
@set ABS_PATH=%CD%
@rem // restore original directory
@popd

@rem Указываю путь к php
@set PATH=%PATH%;%ABS_PATH%
@echo on

xcopy phpunit\*.* ..\vendor\phpunit\*.* /e /i /h /Y
php patch.php
pause