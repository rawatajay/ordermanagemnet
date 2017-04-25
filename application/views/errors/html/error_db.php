<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
	
	<!-- start: HEAD -->
	<head>
		<title>Database Error</title>
		<!-- start: META -->
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta content="" name="description" />
		<meta content="" name="author" />
		<!-- end: META -->
		<!-- start: MAIN CSS -->
                <link rel="shortcut icon" href="<?php echo base_url(); ?>uploads/haturu.ico" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/font-awesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/style.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main-responsive.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/skins/all.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/bootstrap-colorpalette/css/bootstrap-colorpalette.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/perfect-scrollbar/src/perfect-scrollbar.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/theme_light.css" type="text/css" id="skin_color">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/print.css" type="text/css" media="print"/>
		<!--[if IE 7]>
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/font-awesome/css/font-awesome-ie7.min.css">
		<![endif]-->
		
	</head>
	<!-- end: HEAD -->
	<!-- start: BODY -->
	<body class="error-full-page">
		<!-- start: PAGE -->
		<div class="container">
                     <?php 
			$find = "Unable to connect to your database server using the provided settings.";
			$haturu = strpos($message, $find);
			if ($haturu !== false) {
			?>
			<div class="row">
				<!-- start: 500 -->
				<div class="col-sm-12 page-error">
					<div class="error-number bricky">
						DB ERROR!
					</div>
					<div class="error-details col-sm-6 col-sm-offset-3">
						<h3><?php echo $heading; ?></h3>
						<p>
						<?php echo $message; ?><br>First Time?
                                                </p>
                                                <div class="btn-group">
                                                     <a href="install/" class="btn btn-primary"> <i class="icon-cog"></i> Let's Install <?php echo SITE_NAME;?></a>
                                                     <a href="<?php echo base_url();?>home" class="btn btn-default">Return Home</a>
                                                </div>
					</div>
				</div>
				<!-- end: 500 -->
			</div>
                    <?php } else { ?>
                    <div class="row">
				<!-- start: 500 -->
				<div class="col-sm-12 page-error">
					<div class="error-number bricky">
						DB ERROR!
					</div>
					<div class="error-details col-sm-6 col-sm-offset-3">
						<h3><?php echo $heading; ?></h3>
						<p>
                                                    <h4 class="text-success"><b><?php echo $message; ?></b></h4>
                                                </p>
                                                <div class="btn-group">
                                                    <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-danger"><i class="fa fa-chevron-left"></i> Go Back</a>
                                                    <a href="<?php echo base_url();?>home" class="btn btn-default">Return Home</a>
                                                </div>
					</div>
				</div>
				<!-- end: 500 -->
			</div>
                    <?php } ?>
		</div>
		<!-- end: PAGE -->
		<!-- start: MAIN JAVASCRIPTS -->
		<!--[if lt IE 9]>
		<script src="<?php echo base_url(); ?>assets/plugins/respond.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/excanvas.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<!--<![endif]-->
		<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui/jquery-ui-1.10.2.custom.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/blockUI/jquery.blockUI.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/iCheck/jquery.icheck.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/perfect-scrollbar/src/jquery.mousewheel.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/perfect-scrollbar/src/perfect-scrollbar.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/less/less-1.5.0.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/jquery-cookie/jquery.cookie.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-colorpalette/js/bootstrap-colorpalette.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
		<!-- end: MAIN JAVASCRIPTS -->
		<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
		<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
		<script>
			jQuery(document).ready(function() {
				Main.init();
			});
		</script>
	</body>
	<!-- end: BODY -->
</html>