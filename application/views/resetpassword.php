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
    <h1>Reset Password</h1>
    <form method="POST" action="<?php echo base_url('webservice/resetpassword/'.$code);?>">
     <div style="padding: 12px;" id="message"><?php echo $this->session->flashdata('msg'); ?></div>  
      <input type="password" placeholder ="New Password" name="password">
      <input type="password" placeholder="Confirm Password" name="password_again">
      <input type="submit" value="Reset my Password">
    </form>
  </div>
</div>
</body>
</html>