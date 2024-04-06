<?php
    require "functions.php";
    check_login();



    //------------------------------ PROFILE DELETE AND EDIT ------------------------------

    // Profile Delete
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"]) && $_POST["action"] == "delete") { 
        
        $id = $_SESSION["info"]["id"];
        $query = "DELETE from users where id = '$id' limit 1";
        $result = mysqli_query($con, $query);
        
        // This only works if all filenames are unique? I'm not sure what the issue is here but it is deleting all my files - maybe figure out a better way
        // if(file_exists($_SESSION["info"]["image"])) {
        //     unlink($_SESSION["info"]["image"]);
        // }

        $query = "DELETE from posts where user_id = '$id'";
        $result = mysqli_query($con, $query);

        header("Location: logout.php");
        die;
    }

    // Profile Edit
    elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["username"])) { 

        $image_added = false;
        
        // File was uploaded
        if(!empty($_FILES["image"]["name"]) && $_FILES["image"]["error"]== 0) { // && $_FILES["image"]["type"]== "image/jpeg" - won't work with other file types
            $folder = "uploads/";
            
            if(!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            
            $image = $folder.$_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);

            // This only works if all filenames are unique? I'm not sure what the issue is here but it is deleting all my files - maybe figure out a better way
            // if(file_exists($_SESSION["info"]["image"])) {
            //     unlink($_SESSION["info"]["image"]);
            // }

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



    //------------------------------ POSTS DELETE AND EDIT ------------------------------

    // Post Delete
    elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"]) && $_POST["action"] == "post_delete") { 
        
        $id = $_GET["id"] ?? 0; // In case the id was not found, id=0
        $user_id = $_SESSION["info"]["id"];
        
        // This only works if all filenames are unique and it might delete your profile pic if that's the one you used for the post. Figure out a better way    
        // $query = "SELECT * from posts where id = '$id' and user_id = '$user_id' limit 1";
        // $result = mysqli_query($con, $query);
        // if(mysqli_num_rows($result) > 0) {
        //     $row = mysqli_fetch_assoc($result);
        //     if(file_exists($_SESSION["info"]["image"])) {
        //         unlink($_SESSION["info"]["image"]);
        //     }
        // }
        
        $query = "DELETE from posts where id = '$id' and user_id = '$user_id' limit 1";
        $result = mysqli_query($con, $query);

        header("Location: profile.php");
        die;
    }

    // Post Edit
    elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"]) && $_POST["action"] == "post_edit") { 

        $id = $_GET["id"] ?? 0; // In case the id was not found, id=0
        $user_id = $_SESSION["info"]["id"];

        $image_added = false;
        
        // File was uploaded
        if(!empty($_FILES["image"]["name"]) && $_FILES["image"]["error"]== 0) { // && $_FILES["image"]["type"]== "image/jpeg" - won't work with other file types
            $folder = "uploads/";
            
            if(!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            
            $image = $folder.$_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);

            // This only works if all filenames are unique and it might delete your profile pic if that's the one you used for the post. Figure out a better way    
            // $query = "SELECT * from posts where id = '$id' and user_id = '$user_id' limit 1";
            // $result = mysqli_query($con, $query);
            // if(mysqli_num_rows($result) > 0) {
            //     $row = mysqli_fetch_assoc($result);
            //     if(file_exists($_SESSION["info"]["image"])) {
            //         unlink($_SESSION["info"]["image"]);
            //     }
            // }

            $image_added = true;
        }

        $post = addslashes($_POST["post"]);

        if($image_added == true) {
            $query = "UPDATE posts set post = '$post', image = '$image' where id = '$id' and user_id = '$user_id' limit 1";
        }

        else {
            $query = "UPDATE posts set post = '$post' where id = '$id' and user_id = '$user_id' limit 1";
        }

        $result = mysqli_query($con, $query);

        header("Location: profile.php");
        die;
    }



    //------------------------------ ADDING A POST ------------------------------

    elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["post"])) { 

        $image = "";
        
        // File was uploaded
        if(!empty($_FILES["image"]["name"]) && $_FILES["image"]["error"]== 0) { // && $_FILES["image"]["type"]== "image/jpeg" - won't work with other file types
            $folder = "uploads/";
            
            if(!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            
            $image = $folder.$_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        }

        $post = addslashes($_POST["post"]);
        $user_id = $_SESSION["info"]["id"];
        $date = date("Y-m-d H:i:s");

        $query = "INSERT into posts (user_id, post, image, date) values ('$user_id', '$post', '$image', '$date')";

        $result = mysqli_query($con, $query);

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


            <!-- DELETE PROFILE -->
            <?php if(!empty($_GET["action"]) && $_GET["action"] == "delete"):?>
                
                <h2 style="text-align: center;">Are you sure you want to delete your profile?</h2>
                
                <div style="text-align: center; margin: auto; max-width: 600px">

                    <form method="post" enctype="multipart/form-data" style="margin:auto; padding:10px">
                        
                        <img src="<?php echo $_SESSION["info"]["image"] ?>" style="width: 100px; height: 100px; object-fit: cover; margin: auto; display: block;"><br>                    
                        <div> <?php echo $_SESSION["info"]["username"] ?> </div> <br>
                        <div> <?php echo $_SESSION["info"]["email"] ?> </div> <br>
                        <input type="hidden" name="action" value="delete">

                        <button>Delete</button>
                        <a href = "profile.php">
                            <button type="button">Cancel</button>
                        </a>
                    </form> 
                
                </div>

            <!-- DELETE POST -->
            <?php elseif(!empty($_GET["action"]) && $_GET["action"] == "post_delete" && !empty($_GET["id"])):?>
                <?php
                    $id = (int) $_GET["id"];
                    $query = "SELECT * from posts where id = '$id' limit 1";
                    $result = mysqli_query($con, $query);
                ?>

                <?php if(mysqli_num_rows($result) > 0): ?>

                    <?php $row = mysqli_fetch_assoc($result); ?>

                    <h3> Are you sure you want to delete this post?</h3>
                    <form method="post" enctype="multipart/form-data" style="margin:auto; padding:10px;">

                        <img src="<?php echo $row["image"]?>" style="width:100%; height:300px; object-fit: cover;"><br>
                        <div><?php echo $row["post"]?></div><br>
                        <input type="hidden" name="action" value="post_delete">

                        <button>Delete</button>
                        <a href = "profile.php">
                            <button type="button">Cancel</button>
                        </a>
                    </form>

                <?php endif;?>

            <!-- EDIT POST -->
            <?php elseif(!empty($_GET["action"]) && $_GET["action"] == "post_edit" && !empty($_GET["id"])):?>

                <?php
                    $id = (int) $_GET["id"];
                    $query = "SELECT * from posts where id = '$id' limit 1";
                    $result = mysqli_query($con, $query);
                ?>
                
                <?php if(mysqli_num_rows($result) > 0): ?>

                    <?php $row = mysqli_fetch_assoc($result); ?>

                    <h5> Edit a post </h5>
                    <form method="post" enctype="multipart/form-data" style="margin:auto; padding:10px;">

                        <img src="<?php echo $row["image"]?>" style="width:100%; height:300px; object-fit: cover;"><br>
                        Image: <input type="file" name="image"><br>
                        <textarea name="post" rows="8"><?php echo $row["post"]?></textarea><br>
                        <input type="hidden" name="action" value="post_edit">

                        <button>Save</button>
                        <a href = "profile.php">
                            <button type="button">Cancel</button>
                        </a>
                    </form>

                <?php endif;?>

            <!-- EDIT PROFILE -->
            <?php elseif(!empty($_GET["action"]) && $_GET["action"] == "edit"):?>
                
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
            

            <!-- OTHERWISE -->
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

                    <a href="profile.php?action=delete">
                        <button>Delete Profile</button>
                    </a>

                </div>
                <br>

                <hr>
                
                <!-- CREATE A POST -->
                <h5> Create a post </h5>
                <form method="post" enctype="multipart/form-data" style="margin:auto; padding:10px;">
                    
                    Image: <input type="file" name="image"><br>
                    <textarea name="post" rows="8"></textarea><br>

                    <button>Post</button>
                </form>
                
                <hr>

                <!-- DISPLAY THE POSTS -->
                <div>

                    <?php
                        $id = $_SESSION["info"]["id"];
                        $query = "SELECT * from posts where user_id = '$id' order by id desc limit 10";

                        $result = mysqli_query($con, $query);
                    ?>

                    <?php if(mysqli_num_rows($result) > 0):?>

                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>

                            <?php
                                $user_id = $row["user_id"];
                                $query = "SELECT username, image from users where id = '$user_id' limit 1";
                                $result2 = mysqli_query($con, $query);

                                $user_row = mysqli_fetch_assoc($result2);
                            ?>

                            <div style="background-color: white;display:flex; border:solid thin #aaa; border-radius: 10px; margin-bottom:10px;margin-top:10px;">

                                <div style="flex:1; text-align: center;">
                                    <img src="<?php echo $user_row["image"]?>" style="border-radius: 50%; margin:10px; width:100px; height:100px; object-fit: cover;">
                                    <br>
                                    <?php echo $user_row["username"]?>
                                </div>

                                <div style="flex:8"> 
                                    <?php if(file_exists($row["image"])):?>
                                        <div>
                                            <img src="<?php echo $row["image"]?>" style="width:100%; height:300px; object-fit: cover;">
                                        </div>
                                    <?php endif;?>

                                    <div>

                                        <div style="color:#888"> <?php echo date("jS M, Y", strtotime($row["date"]))?> </div>
                                        <?php echo nl2br(htmlspecialchars($row["post"])) 
                                        // nl2br - changes newline to break tags
                                        // htmlspecialchars - to avoid running js scripts in posts - keep it plaintext?> 

                                        <br><br>
                                        <a href="profile.php?action=post_edit&id=<?php echo $row["id"]?>">
                                            <button>Edit</button>
                                        </a>

                                        <a href="profile.php?action=post_delete&id=<?php echo $row["id"]?>">
                                            <button>Delete</button>
                                        </a>
                                        <br><br>

                                    </div>
                                </div>
                            
                            </div>

                        <?php endwhile;?>

                    <?php endif;?>

                </div>

            
            <?php endif;?>

        </div>
        
        <?php require "footer.php"; ?>

    </body>
</html>
