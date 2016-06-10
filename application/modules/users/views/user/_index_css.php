<?php
JsCssFiles::cssFile(Yii::app()->baseUrl.'/css/urv-table.css');

JsCssFiles::css('
/* Стили, применимые к этой странице
----------------------------------------------------------------*/
.urv-table > tbody > tr > td, .urv-table > thead > tr > th {padding: 3px 10px;}
.urv-table .no-top-border-in-child th {border-top: 0; font-weight: inherit; color:inherit;}
.urv-table > tbody > tr > td {height: 30px;}
table.urv-table tbody tr.odd:hover, table.urv-table tbody tr.even:hover {background-color:#dfe7fe; cursor:pointer;}
table.urv-table {color:inherit;}
table.urv-table tbody, table.urv-table th {color:#000379;}

#registration-form .format input[type="text"] {width:90%;}
#registration-form .format input[type="password"] {width:90%;}

.urv-table tr.user-blocked {color:#ccc;}
.urv-table .subscribed{color:green;}
.urv-table .unsubscribed{color:red;}
.urv-table .check-ip{color:red;}
');