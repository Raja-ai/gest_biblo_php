<?php
include "config.php";

// Check user login or not
if(!isset($_SESSION['uname'])){
    header('Location: index.php');
}

// logout
if(isset($_POST['but_logout'])){
    session_destroy();
    header('Location: index.php');
}
?>
<!doctype html>
<html>

<head>
    <script src="js/jquery-3.3.1.js" type="text/javascript"></script>
</head>
<body>
<h1>Homepage</h1>
<form method='post' action="">
    <input type="submit" value="Logout" name="but_logout">
</form>
<script>
    $(document).ready(function(){
        $("#but_submit").click(function(){
            var username = $("#txt_uname").val().trim();
            var password = $("#txt_pwd").val().trim();

            if( username != "" && password != "" ){
                $.ajax({
                    url:'checkUser.php',
                    type:'post',
                    data:{username:username,password:password},
                    success:function(response){
                        var msg = "";
                        if(response == 1){
                            window.location = "home.php";
                        }else{
                            msg = "Invalid username and password!";
                        }
                        $("#message").html(msg);
                    }
                });
            }
        });
    });
</script>
</body>
</html>