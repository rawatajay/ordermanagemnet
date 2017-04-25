<!DOCTYPE html>
<html>
    <head>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.11.1.min.js" ></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pretty-json.css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Euphoria</title>

        <style>
            body{
                font-family: 'Quicksand', sans-serif;
            }
            .form_row{margin-bottom: 5px;}
            .form_label{display: inline-block;width: 250px;text-align: right;}
            .form_field{display: inline-block; vertical-align: middle;}
            .div_service{margin-top:4%;padding-bottom:10%;}
            .list_1 li{padding: 5px 0;}
            .list_1 li a{color: #18a018;font-weight: bold;}
            .list_1 {
                counter-reset: li; /* Initiate a counter */
                list-style: none; /* Remove default numbering */
                *list-style: decimal; /* Keep using default numbering for IE6/7 */
                font: 15px 'trebuchet MS', 'lucida sans';
                padding: 0;
                margin-bottom: 4em;
                text-shadow: 0 1px 0 rgba(255,255,255,.5);
                margin-left: 1.1em;

            }
            .list_1 a{
                position: relative;
                display: block;
                padding: .4em .4em .4em 2em;
                *padding: .4em;
                margin: .5em 0;
                background: #ddd;
                color: #444;
                text-decoration: none;
                border-radius: .3em;
                transition: all .3s ease-out;
                width: 40%;
            }

            .list_1 a:hover{
                background: #eee;
            }

            .list_1 a:hover:before {
                background: none repeat scroll 0 0 #87ceeb;
                border: 0.3em solid #fff;
                border-radius: 2em;
                content: counter(li, decimal);
                counter-increment: li;
                font-weight: bold;
                height: 2em;
                left: -1.3em;
                line-height: 2em;
                margin-left: 0.1em;
                margin-top: -1.3em;
                position: absolute;
                text-align: center;
                top: 50%;
                transform: rotate(360deg);
                transition: all 0.3s ease-out 0s;
                width: 2em;
                color: #fff;
            }

            .list_1 a:before {
                background: none repeat scroll 0 0 #fa8072;
                content: counter(li, decimal);
                counter-increment: li;
                font-weight: bold;
                height: 2.1em;
                left: -2.5em;
                line-height: 2em;
                margin-left: 1.4em;
                margin-top: -1.1em;
                position: absolute;
                text-align: center;
                top: 50%;
                transition: all 0.3s ease-out 0s;
                width: 2em;
                color: #000;
            }

            input[type="text"],input[type="password"],  textarea{
                width: 250px;
                height: 25px;
                border: 1px solid #97ec97;
                padding: 5px;

            }
            textarea{
                height: 50px;
            }
            h3, h2 {
                color: #1383e4;
            }
            select{
                width: 262px;
                border: 1px solid #97ec97;
                height: 35px;
                background-color: #fff;
            }
            .form_field .desc{
                width: auto;
                float: right;
                margin-left:10px;
                font-size: 14px;
                color: #080;
            }
            input[type="submit"], input[type="button"] {
                background-color: #58bf58;
                border: 1px solid #97ec97;
                border-radius: 6px;
                color: #fff;
                cursor: pointer;
                height: 40px;
                padding: 5px;
                width: 140px;
            }
            a{
                text-decoration: none;
            }
            select option {
                height: 25px;
                vertical-align: middle;
            }
            select option:hover{
                background-color:#97ec97;
            }
            p {
                color: #1383e4;
                font-size: 13px;
                font-weight: normal;
                margin: 0;
                padding-left: 2em;
            }
            .multi-list {
                display: inline-block;
                height: auto;
                max-height: 70px;
            }
            ul {
                list-style: none outside none;
            }
            ul li a {
                color: #080;
                font-weight: 500;
                line-height: 1.5;
                text-decoration: none;
            }
            input[type="checkbox"] {
                width: 20px;
                height: 20px;
                border: 1px solid #080;
            }
        </style>
        <script>
            $(document).ready(function () {
                //  $('form').attr('autocomplete','off');
                $(".mydate").datepicker({showAnim: 'slideDown', dateFormat: 'yy-mm-dd'});
                $('.div_service').hide();
                $("#back-top").hide();
                var showID = location.hash;
                if (showID) {
                    $(showID).fadeIn();
                    setTimeout(function () {
                        $("html, body").animate({scrollTop: $(showID).offset().top});
                    }, 500);
                }

                $(function () {
                    $(window).scroll(function () {
                        if ($(this).scrollTop() > 100) {
                            $('#back-top').fadeIn();
                        } else {
                            $('#back-top').fadeOut();
                        }
                    });

                    // scroll body to 0px on click
                    $('#back-top').click(function () {
                        $('body,html').animate({
                            scrollTop: 0
                        }, 500);
                        return false;
                    });

                    $('#module-1, #module-2').click(function () {
                        $('body,html').animate({
                            scrollTop: $($(this).attr('href')).offset().top
                        }, 500);
                        return false;
                    });
                    $('body input,body textarea, body select ').each(function () {
                        if ($(this).next('div').html() == "") {
                            $(this).next('div').html('Key: ' + $(this).attr('name'));
                        }
                    });
                });


                $('.list_1 > li > a').on('click', function () {
                    $('#search').val($(this).text());

                    $('.div_service').fadeOut(500);
                    var showID = $(this).data('href');
                    $('#search_id').val(showID);
                    $(showID).fadeIn(1000);
                    setTimeout(function () {
                        $("html, body").animate({scrollTop: $(showID).offset().top});
                    }, 500);
                });

                $('#reg_type').on('change', function () {
                    var regType = $(this).val();
                    if (regType == 'normal') {
                        $('.normal, #social').slideUp();
                        $('#normal').slideDown();
                    } else {
                        $('.normal').slideDown();
                        $('#normal').slideUp();
                        $('#social').slideDown();
                    }
                });

                $("#search").on('keyup change blur', function (e) {
                    if (e.type == "keyup") {
                        $('#search_id').val('');
                    }
                    //   $('li a').trigger('click');

                    $('.div_service').hide();
                    // Retrieve the input field text and reset the count to zero
                    var filter = $(this).val(), count = 0;

                    // Loop through the comment list
                    $(".list_1 li a").each(function () {
                        // If the list item does not contain the text phrase fade it out
                        var anchorText = $(this).text().search(new RegExp(filter, "i"));
                        var serviceUrl = $(this).next('p').text().search(new RegExp(filter, "i"))
                        if (anchorText < 0 && serviceUrl < 0) {
                            $(this).parents('li').fadeOut();

                            // Show the list item if the phrase matches and increase the count by 1
                        } else {
                            $(this).parents('li').show();
                            $($('#search_id').val()).show();
                            // $(this).click();
                            // $($(this).data('href')).show();
                            count++;
                        }
                    });
                    //$('#search_id').val('');
                }).trigger('change');

            });
        </script>
    </head>
    <body>
       

        <h2 id="module1">Welcome to Order Management</h2>

        
    </body>
</html>


  
  
  
  
  