@echo off
@rem Получаю абсолютный путь из относительного: http://stackoverflow.com/a/4488734/3551026
@set REL_PATH=..\..\..\web-server\vendor\php-5.4.45-nts-Win32-VC9-x86
@rem @set REL_PATH=..\..\..\web-server\php-5.4.33-Win32-VC9-x86
@set ABS_PATH=
@rem // save current directory and change to target directory
@pushd %REL_PATH%
@rem // save value of CD variable (current directory)
@set ABS_PATH=%CD%
@rem // restore original directory
@popd

@rem Указываю путь к php
@set PATH=%PATH%;%ABS_PATH%

@rem Запускаю selenium-server
@start java -jar ..\..\..\vendors\vendor\se\selenium-server-standalone\bin\selenium-server-standalone.jar

@echo on