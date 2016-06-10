<?php JsCssFiles::css(
<<<EOL
html body {background-image: url("/images/login-form.jpg"); }
body #content, body #footer {padding: 0; margin:0;}
html, body, #content {height: 100%;}

.login-form-wrapper {
	background-image: url("/images/login-form-wrapper.png");
	width: 714px;
	height: 287px;
	margin: 0 auto;
	position: relative;
	margin-top: -80px;
}
#login-form {
	padding-top: 55px;
	padding-left: 220px;
	margin: 0 auto;
}

.login-text-field {
	width: 230px;
	height: 22px;
	margin-top: 15px;
	padding: 4px 15px;
	border: 1px solid #9e9e9e;
	border-radius: 5px;
	font-style: italic;
	font-family: Georgia, Arial;
	font-size: 18px;
	
	box-shadow: 0 1px 10px rgba(0, 0, 0, 0.15) inset;
    transition: border-color 0.3s ease-in-out 0s, box-shadow 0.3s ease-in-out 0s;
}

.login-text-field:focus {
	border-color: #aeaeae;
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(80, 80, 100, 0.6);
	outline: 0 none;
}

label[for="LoginForm_rememberMe"] {
	color: #05054d;
	font-size: 13px;
	font-style: italic;
	font-weight: bold;
}
.login-form-wrapper .rememberMe {
	margin-top: 15px;
}

.login-form-wrapper input[type="submit"] {
	padding: 5px 20px;
	background: #192d4c;
	border: 1px solid #000000;
	border-radius: 5px;
	color: #ccd3ea;
	width: 135px;
	margin-top: 15px;
	cursor: pointer;
	height: 30px;
	font-weight: bold;
	font-style: normal;
	font-size: 14px;
}
.login-form-wrapper input[type="submit"]:hover {
	background: #293d5c;
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(80, 80, 100, 0.6);
}
.login-form-wrapper input[type="submit"]:active {
	background: #091d3c;
	box-shadow: none;
}

body .error {
	color: #a00;
	text-shadow: 1px 1px rgba(226, 157, 156, 0.7);
}



/* Детализация формы
---------------------------------------------------------------*/
.login-form-reflection {
	background: url("/images/login-form-reflection.png") no-repeat;
	position: absolute;
	top: -2px;
	left: 50%;
	width: 714px;
	height: 140px;
	margin: 0 auto;
	margin-left: -336px;
}

.your-ip {
	color: #ffff00;
	font-size: 16px;
	font-family: Arial;
	margin-left: 230px;
}

.instruction {
	color: #ffffff;
	font-size: 13px;
	font-style: italic;
	font-weight: bold;
	width: 400px;
	margin: 0 auto;
	padding-left: 35px;
	font-family: Verdana, Arial;
}
.instruction a {
	text-decoration: underline;
	color: #06f6ff;
}
.instruction a:hover {
	color: #a0ffff;
}


.login-footer {
	position: absolute;
	bottom: 0px;
	left: 0px;
	width: 100%;
	height: 50px;
	color: #ffffff;
	text-align: center;
	font-style: italic;
	font-size: 14px;
	font-family: monospace, Verdana, Arial;
}

.login-footer-line {
	height: 3px;
	width: 100%;
	margin-bottom: 5px;
	
	/* IE9 SVG, needs conditional override of 'filter' to 'none' */
	background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIxMDAlIiB5Mj0iMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzFmMjMyNCIgc3RvcC1vcGFjaXR5PSIwIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjUwJSIgc3RvcC1jb2xvcj0iIzFmMjMyNCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMxZjIzMjQiIHN0b3Atb3BhY2l0eT0iMCIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
	background: -moz-linear-gradient(left,  rgba(31,35,36,0) 0%, rgba(31,35,36,1) 50%, rgba(31,35,36,0) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(31,35,36,0)), color-stop(50%,rgba(31,35,36,1)), color-stop(100%,rgba(31,35,36,0))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(left,  rgba(31,35,36,0) 0%,rgba(31,35,36,1) 50%,rgba(31,35,36,0) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(left,  rgba(31,35,36,0) 0%,rgba(31,35,36,1) 50%,rgba(31,35,36,0) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(left,  rgba(31,35,36,0) 0%,rgba(31,35,36,1) 50%,rgba(31,35,36,0) 100%); /* IE10+ */
	background: linear-gradient(to right,  rgba(31,35,36,0) 0%,rgba(31,35,36,1) 50%,rgba(31,35,36,0) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#001f2324', endColorstr='#001f2324',GradientType=1 ); /* IE6-8 */
}

/* Ссылка "Забыли пароль"
---------------------------------------------------------------*/
.forgot-password-link {
	position: absolute;
	top: 130px;
	left: 495px;
}

.forgot-password-link a, #registration-form-link, #recovery-captcha_button {
	text-decoration: none;
	color: #2000dd;
	font-style: italic;
	font-size: 13px;
	font-weight: bold;
	font-family: Hevletica, Arial;
}
.forgot-password-link a:hover {text-decoration: underline;}

/* Ссылка "Регистрация"
---------------------------------------------------------------*/
#registration-form-link {
	color: #d00;
	font-family: Verdana, Arial;
	font-size: 14px;
	font-weight: bold;
	font-style: italic;
	text-decoration: none;
}
#registration-form-link:hover {
	text-decoration: underline;
}

/* Форма восстановления пользователя
---------------------------------------------------------------*/
.recovery-form-wrapper {
	width: 646px;
	background: #bbb;
	background: rgba(200,200,200, 0.5);
	border-radius: 3px;
	padding: 5px 10px;
}

.email-or-username {
	float: left;
	margin: 0;
	font-size: 16px;
	width: 300px;
}
.email-or-username input[type="text"] {
	margin:0;
	width: 270px;
}
.email-or-username .error {
	width: 655px;
	font-size: 13px;
}

#recovery-form-button {
	float: left;
	margin-left: 10px;
	
	background: #2f2f6e;
    background: linear-gradient(#4d4d6d, #2f2f6f) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: 1px solid black;
    border-radius: 5px;
    color: white !important;
    cursor: pointer;
    font-size: 1.2em;
    line-height: 1.4;
    outline: 0 none;
    padding: 3px 16px;
    text-decoration: none !important;
    text-overflow: ellipsis;
    white-space: nowrap;
}
#recovery-form-button:hover {
	background: linear-gradient(#5d5d7d, #3f3f7f) repeat scroll 0 0 rgba(0, 0, 0, 0);
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(80, 80, 100, 0.6);
}
#recovery-form-button:active {
	box-shadow: none;
}


.captcha-wrapper {
	margin-top: 10px;
}
.captcha-wrapper:after {
	display: table;
	content: '';
	clear: both;
}
.captcha-wrapper .error {
	float: left;
	margin-left: 10px;
	margin-top: 4px;
}

#recovery-captcha {
	float: left;
	
}

#RecoveryForm_verifyCode {
	float: left;
	margin-left: -80px;
	padding: 2px 11px;
	border-radius: 4px;
	width: 145px;
}
#recovery-captcha_button {
	float: left;
	margin-left: 16px;
	margin-top: 27px;
}
#recovery-captcha_button:hover {
	text-decoration: underline;
}
EOL
);