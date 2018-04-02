<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title>CMXpress User's Manual</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>

<frameset cols="210,1*">
	<frame name="toc" src="toc.html" />
	<frame name="content" src="<?php
	if (isset($_REQUEST['page']) && preg_match('/^[a-zA-Z0-9_]+$/', $_REQUEST['page']))
		print $_REQUEST['page'] . ".html";
	else print "intro_over.html";
?>" />
</frameset>

<noframes>Sorry, this user's manual requires a browser with support for frames.</noframes>

</html>
