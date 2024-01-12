<?php
// include our connect script
require_once("conf.php");

// check to see if there is a user already logged in, if so redirect them

if (isset($_SESSION['username']) && isset($_SESSION['userid']))
    echo '<script>window.location.href = "haldusLeht.php";</script>';  // redirect the user to the home page
if (isset($_POST['registerBtn'])){
    // get all of the form data
    $username = $_POST['username'];
    $passwd = $_POST['passwd'];
    $passwd_again = $_POST['passwd_again'];


    // query the database to see if the username is taken
    global $yhendus;
    $kask= $yhendus->prepare("SELECT * FROM kasutaja WHERE kasutaja=?");
    $kask->bind_param("s",$username);
    $kask->execute();
    //$query = mysqli_query($yhendus, "SELECT * FROM kasutajad WHERE nimi='$username'");
    if (!$kask->fetch()){

        // create and format some variables for the database
        $id = '';
        $cool='superpaev';
        $krypt=crypt($passwd, $cool);
        $passwd_hashed = $krypt;
        $date_created = time();
        $last_login = 0;
        $status = 1;



        // verify all the required form data was entered
        if ($username != "" && $passwd != "" && $passwd_again != ""){
            // make sure the two passwords match
            if ($passwd === $passwd_again){
                // make sure the password meets the min strength requirements
                if ( strlen($passwd) >= 5 && strpbrk($passwd, "!#$.,:;()")){
                    // insert the user into the database
                    mysqli_query($yhendus, "INSERT INTO kasutaja (kasutaja, parool) VALUES ('$username', '$passwd_hashed')");
                    //echo "<script>alert('rrrr')</script>";
// verify the user's account was created
                    $query = mysqli_query($yhendus, "SELECT * FROM kasutaja WHERE kasutaja='{$username}'");
                    if (mysqli_num_rows($query) == 1){

                        /* IF WE ARE HERE THEN THE ACCOUNT WAS CREATED! YAY! */
                        /* WE WILL SEND EMAIL ACTIVATION CODE HERE LATER */
//echo "<script>alert('yay')</script>";
                        $success = true;
                    }
                }
                else
                    $error_msg = 'Your password is not strong enough. Please use another.';
            }
            else
                $error_msg = 'Your passwords did not match.';
        }
        else
            $error_msg = 'Please fill out all required fields.';
    }
    else
        $error_msg = 'The username <i>'.$username.'</i> is already taken. Please use another.';
}

else
    $error_msg = 'An error occurred and your account was not created.';


?>


<?php
// Kui on registreeritud -> näeme tulemusena jah/ei registreerimisel
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Registreerinud
    if (isset($success) && $success){
        echo '<div class=""><p style="color:green;">Yay!! Your account has been created.</p></div>';
    }
    // Ei saanud registreerida
    else if (isset($error_msg)){
        echo '<div class=""><p style="color:red;">'.$error_msg.'</p></div>';
    }
} else {
    // Reg. vorm
    ?>
    <form action="./register.php" class="form" method="POST">

        <h1>Registreeri uus kasutaja</h1>

        <div class="">
            <input type="text" name="username" value="" placeholder="enter a username" autocomplete="off" required />
        </div>
        <div class="">
            <input type="password" name="passwd" value="" placeholder="enter a password" autocomplete="off" required />
        </div>
        <div class="">
            <p>password must be at least 5 characters and<br /> have a special character, e.g. !#$.,:;()</font></p>
        </div>
        <div class="">
            <input type="password" name="passwd_again" value="" placeholder="confirm your password" autocomplete="off" required />
        </div>

        <div class="">
            <input class="" type="submit" name="registerBtn" value="create account" />
        </div>

        <!--
        <p class="center"><br />
            Already have an account? <a href="login.php">Login here</a>
        </p>
        -->
    </form>
    <?php
}
?>
