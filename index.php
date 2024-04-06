<?php
    require "functions.php";

    check_login();
?>

<!DOCTYPE html>

<html>
    <head>
        <title>PHP Website</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    
    <body>

        <?php require "header.php"; ?>
        
        <div style="max-width:600px; margin:auto;">

            <h3 style="text-align:center;">Timeline</h3>

            <?php
                $id = $_SESSION["info"]["id"];
                $query = "SELECT * from posts order by id desc limit 10";

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
                                <?php echo nl2br(htmlspecialchars($row["post"])) ?>
                            </div>
                        </div>
                    
                    </div>

                <?php endwhile;?>

            <?php endif;?>

        </div>
        
        <?php require "footer.php"; ?>

    </body>
</html>