<?php
    require "functions.php";

    check_login();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["username"])) { // Profile Edit

        $image_added = false;

        if(!empty($_FILES["image"]["name"]) && $_FILES["image"]["error"]== 0) { // File was uploaded
            $folder = "uploads/";
            
            if(!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            
            $image = $folder.$_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);

            if(file_exists($_SESSION["info"]["image"])) {
                unlink($_SESSION["info"]["image"]);
            }

            $image_added = true;
        }

        $username = addslashes($_POST["username"]);
        $email = addslashes($_POST["email"]);
        $password = addslashes($_POST["password"]);
        $id = $_SESSION["info"]["id"];

        if($image_added == true) {
            $query = "UPDATE users set username = '$username', email = '$email', password = '$password', image = '$image' where id = '$id' limit 1";
        }

        else {
            $query = "UPDATE users set username = '$username', email = '$email', password = '$password' where id = '$id' limit 1";
        }

        $result = mysqli_query($con, $query);

        $query = "SELECT * from users where id = '$id' limit 1";
        $result = mysqli_query($con, $query);

        if(mysqli_num_rows($result) > 0) {
            $_SESSION["info"] = mysqli_fetch_assoc($result);
        }

        header("Location: profile.php");
        die;
    }
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

            <?php if(!empty($_GET["action"]) && $_GET["action"] == "edit"):?>
                
                <h2 style="text-align: center;">Edit Profile</h2>

                <form method="post" enctype="multipart/form-data" style="margin:auto; padding:10px">
                    
                    <img src="<?php echo $_SESSION["info"]["image"] ?>" style="width: 100px; height: 100px; object-fit: cover; margin: auto; display: block;"><br>
                    Image: <input type="file" name="image"><br>
                    
                    <input value="<?php echo $_SESSION["info"]["username"] ?>" type="text" name="username" placeholder="Username" required><br>
                    <input value="<?php echo $_SESSION["info"]["email"] ?>"type="email" name="email" placeholder="Email" required><br>
                    <input value="<?php echo $_SESSION["info"]["password"] ?>"type="text" name="password" placeholder="Password" required><br>

                    <button>Save</button>
                    <a href = "profile.php">
                        <button type="button">Cancel</button>
                    </a>
                </form>

            <?php else:?>

                <h2 style="text-align: center;">User Profile</h2>

                <br>
                <div style="text-align: center; margin: auto; max-width: 600px">
                    <div>
                        <td> <img src="<?php echo $_SESSION["info"]["image"] ?>" style="width: 150px; height: 150px; object-fit: cover;"> </td>
                    </div>

                    <div>
                        <td><?php echo $_SESSION['info']['username']?></td>
                    </div>

                    <div>
                        <td><?php echo $_SESSION['info']['email']?></td>
                    </div>

                    <a href="profile.php?action=edit">
                        <button>Edit Profile</button>
                    </a>

                </div>
                <br>

                <hr>

                <h5> Create a post </h5>
                <form method="post" style="margin:auto; padding:10px;">
                    <textarea name="post" rows="8"></textarea><br>

                    <button>Post</button>
                </form>
            
            <?php endif;?>

        </div>
        
        <?php require "footer.php"; ?>

    </body>
</html>