<?php
    require "functions.php";

    check_login();


?>

<!DOCTYPE html>

<html>
    <head>
        <title>Profile - PHP Website</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    
    <body>

        <?php require "header.php"; ?>

        <div style="margin:auto; max-width:600px;">

            <h2 style="text-align: center;">User Profile</h2>

            <br>
            <div style="text-align: center; margin: auto; max-width: 600px">
                <div>
                    <td> <img src="img.jpg" style="width: 150px; height: 150px; object-fit: cover;"> </td>
                </div>

                <div>
                    <td><?php echo $_SESSION['info']['username']?></td>
                </div>

                <div>
                    <td><?php echo $_SESSION['info']['email']?></td>
                </div>

            </div>
            <br>
            
            <hr>

            <h5> Create a post </h5>
            <form method="post" style="margin:auto; padding:10px;">
                <textarea name="post" rows="8"></textarea><br>

                <button>Post</button>
            </form>
            
        </div>
        
        <?php require "footer.php"; ?>

    </body>
</html>