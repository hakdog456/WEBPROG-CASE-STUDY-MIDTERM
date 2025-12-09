<!-- PHP -->                
        <?php
            use Dom\Mysql;
            include("database.php");

            // ADDING A NEW ACC TEST
            // $name = 'wowowow';
            // $username = 'mauwowie';
            // $password = 'heynow';
            // $priveledge = 'user';

            // $sql = "INSERT INTO users (name, username, password, priveledge)
            // VALUES ('$name', '$username', '$password', '$priveledge')";

            // mysqli_query($conn, $sql);

            // mysqli_close($conn);

            echo "<h1>this is the index</h1>";

            // QUERYING TEST
            $sql = "SELECT * FROM users WHERE username = 'hakdog'";
            $result = mysqli_query($conn, $sql);

            if(mysqli_num_rows($result) > 0){
                $row = mysqli_fetch_assoc($result);
                echo $row["id"] . "<br>";
            }
            

            mysqli_close($conn);

        ?>