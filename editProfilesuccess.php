<?php
 require 'conn.php';
   $sql_update="UPDATE memberbio SET m_name='$_POST[m_name]',m_lastname='$_POST[m_lastname]' ,address='$_POST[address]' ,telephone='$_POST[telephone]' WHERE m_id='$_POST[m_id]' ";

            $result= $conn->query($sql_update);

            if(!$result) {
                die("Error God Damn it : ". $conn->error);
            } else {
            header("refresh: 1; url=m_mainmenu.php");
            }

?>
