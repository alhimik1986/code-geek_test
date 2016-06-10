call cmd_conf\cmd_conf.bat

cd ..
..\..\vendors\vendor\bin\phpunit.bat --log-tap report\report.txt functional/modules/settings
pause