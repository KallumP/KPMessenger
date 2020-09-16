<?php
//if (logged out)
?>
<div class="Login">
    <form action="includes/zLogin.inc.php" method="POST">
        <a href="createNewAccount.php">Create new account</a>
        <input class="username_txt" type="text" name="username" placeholder="Enter username">
        <input class="password_txt" type="password" name="password" placeholder="Enter password">
        <button class="login_btn" type="submit" name="login"> Login </button>
    </form>
</div>


<?php
//if (logged in)
?>
<!-- 
<div class="Login">
    <a href="createNewAccount.php">Create new account</a>
    <h2>Logged in as User: username || ID: id</h2>
</div> -->