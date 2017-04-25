<?php

//if($showHtml == 'yes' || $_SERVER['REQUEST_METHOD'] == 'GET'){ ?>

<html>
<head>
    <title><?php echo SITE_TITLE;?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pretty-json.css" />

    <!-- lib -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.11.1.min.js" ></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/underscore-min.js" ></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/backbone-min.js" ></script>

    <!-- pretty JSON v 0.1  -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pretty-json-min.js" ></script>

    <!-- just css for this page example -->
    <style type="text/css">
        body{
            width:700px;
            border-style: none;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
<script>
    $(document).ready(function() {

        var el = {
            btnAction: $('#action'),
            btnClear: $('#clear'),
            input: $('#input'),
            result: $('#result')
        };
          var json =JSON.stringify(<?php echo $jsonData; ?>,null,4);
            var o;
            try{ o = JSON.parse(json); }
            catch(e){
                alert('not valid JSON');
                return;
            }
            var node = new PrettyJSON.view.Node({
                el:el.result,
                data:o
            });
        el.btnClear.on('click', function(){
           history.go(-1);
        });
    });
</script>

<h1>Json Result</h1>


<span id="result"></span>
<br/>

</body>
</html>
<?php //}else{

   // echo $jsonData;
//}

?>

