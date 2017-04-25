<!DOCTYPE HTML>
<html>
<head>
<title><?php echo SITE_TITLE?> : User Account Activation</title>
<link href="<?php echo base_url()?>assets/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<!-- Custom Theme files -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 

<link href='//fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
</head>
<body>

<div class="elelment">
  <h2><img src="<?php echo base_url()?>assets/images/logo.png"/></h2>
  <div class="element-main">
    <h1>Account Activation</h1>    
     <div style="padding: 12px;" id="message"><?php echo $this->session->flashdata('msg'); ?></div>      
  </div>
</div>
</body>
</html>