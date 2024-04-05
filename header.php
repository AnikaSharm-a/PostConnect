<style>
    button {
        box-sizing: border-box;
        padding: 10px;
        cursor: pointer;
    }
</style>

<header>
    <div><a href="index.php">Home</a></div>
    <div><a href="profile.php">Profile</a></div>

    <?php if(empty($_SESSION['info'])): ?>
        <div><a href="login.php">Login</a></div>
        <div><a href="signup.php">Signup</a></div>

    <?php else: ?>
        <div><a href="logout.php">Logout</a></div>
        
    <?php endif; ?>

</header>
