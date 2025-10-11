<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Videostore</title>
</head>
<body class="m_body">
  <h1 class="m_index">สมาชิก</h1><br>  
        <table class="m_table">
            <thead>
                <tr>
                    <th>User_id</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th> ธุรกรรม </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $conn = new mysqli('localhost','root','','picture_store');
                $conn->query("SET NAMES utf8");
                if($conn->connect_error){
                    die("Connection Fail: ". $conn->connect_error);
                }

                $sql = "SELECT * FROM memberbio";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr><td>".$row["m_id"]."</td><td>".$row["m_name"]." ".$row["m_lastname"]."</td><td>".$row["address"]."</td><td>".$row["telephone"]."</td><td><a href='m_editbio.php?m_id=".$row["m_id"]."'><button> Edit </button></a></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>0 results</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table> 
        <br>
        <a class="m_button2" href='AdminMainmenu.php'><button> Home </button></a>
    </body>
</html>
