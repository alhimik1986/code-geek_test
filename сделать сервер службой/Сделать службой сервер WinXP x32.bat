@rem Выбор утилиты для создания службы в зависимости от разрядности операционной системы
if %PROCESSOR_ARCHITECTURE%==x86 (
  cd %~dp0\nssm\win32 
) else (
  cd %~dp0\nssm\win64
)

@rem Получаю абсолютный путь из отностительного
set rel_path=..\..\..\web-server\trays
pushd %rel_path%
set abs_path=%CD%
popd

@rem Создаю службу с именем NginxTrayRu
nssm install NginxTrayRu %ABS_PATH%\NginxTrayRu_php_5_4.exe
@rem Запускаю эту службу
net start NginxTrayRu

pause