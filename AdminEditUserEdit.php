<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Edit User</title>
</head>
<body class="m_bodyinsert">
   <?php
        if(!isset($_GET['m_id'])){
            header("refresh: 0; url=m_mainmenu.php");
        }
        require 'm_conn.php';
        $sql = "SELECT * FROM memberbio WHERE m_id='$_GET[m_id]'";
        $result = $conn->query($sql);
        $row = mysqli_fetch_array($result);
    ?>

    <form method="post" action="AdminEditUserEditSuccess.php">
        <h1>แก้ไขข้อมูลสมาชิก</h1>
            <div class="m_divinsert">
            <div class="m_divinsert_input-box">
                <div class="m_divinsert_left">
                    <p class="title">ชื่อ</p>
                </div>
                <div class="m_divinsert_right">
            <input type="text" name="m_id" id="m_id" value="<?=$row['m_id'];?>" hidden>
            <input type="text" name="m_name" id="m_name" value="<?=$row['m_name'];?>" />
                </div>
            </div>  
             <div class="m_divinsert_input-box">
                <div class="m_divinsert_left">
                    <p class="title">นามสกุล</p>
                </div>
                <div class="m_divinsert_right">
            <input type="text" name="m_lastname" id="m_lastname" value="<?=$row['m_lastname'];?>" />
                </div>
            </div>  
             <div class="m_divinsert_input-box">
                <div class="m_divinsert_left">
                    <p class="title">ที่อยู่</p>
                </div>
                <div class="m_divinsert_right">
            <input type="text" name="address" id="address" value="<?=$row['address'];?>" />
                </div>
            </div>  
             <div class="m_divinsert_input-box">
                <div class="m_divinsert_left">
                    <p class="title">เบอร์โทร</p>
                </div>
                <div class="m_divinsert_right">
            <input type="text" name="telephone" id="telephone" value="<?=$row['telephone'];?>" />
                </div>
            </div>  

        <input class="m_button_insert" type="submit" value="บันทึก">
        
    </form>
   
     <a class="m_button_insert2" href='m_mainmenu.php'><button>Back</button></a>
    </div>
</body>
</html>