@net stop NginxTrayRu
@cd %~dp0\web-server\patches\patch_production_php_5_4
call patch.bat
@cd ..\..\trays
start NginxTrayRu_php_5_4.exe
start http://localhost