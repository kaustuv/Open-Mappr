<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class=""> <!--<![endif]-->
<head>
<?php
$this->load->view('html/meta_view');
?>
</head>
<body>
<div id='main-container'>
<?php
$this->load->view('html/header_view');
$this->load->view('html/menu_view');
?>
<div id='page'>
<?php
$this->load->view($page);
?>
</div>
<?php
$this->load->view('html/footer_view');
?>
</div>
</body>
</html>