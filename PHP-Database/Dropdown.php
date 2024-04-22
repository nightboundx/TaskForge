                <?php
                //These values are for the connection to the SQL server
                $server_name = "localhost";
                $username = "webapp_select";
                $password = "JDqyb)f])Pl!8y5.";
                $database = "assessment-2";

                //Establishing a new connection to the server
                $connDropdown = new mysqli($server_name, $username, $password);

                if ($connDropdown->connect_error){
                    die("Connection failed: " . $connDropdown->connect_error);
                }

                $stmt = $connDropdown->prepare("SELECT * from credentials.users");
                $stmt->execute();
                $result = $stmt->get_result();
                //Iterate through the results
                while ($row = $result->fetch_assoc()) {
                    // Access the user_id, forename, and surname from the $row array
                    $userid = htmlspecialchars($row['user_id']);
                    $forename = htmlspecialchars($row['forename']);
                    $surname = htmlspecialchars($row['surname']);
                    echo '<option value="'.$userid.'">'.$forename . ' ' . $surname .'</option>';
                }
                $connDropdown->close();
                ?>