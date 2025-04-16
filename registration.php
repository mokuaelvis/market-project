<?php

session_start(); 

if(!isset($_SESSION['username']))
{
    header("location;login.php");
}
elseif($_SESSION['usertype'])'client';
{
    header("location;login.php");
}

$host="localhost";
$user="root";
$password="";
$db="marketproject";

$data=mysqli_connect($host,$user,$password,$db);

$sql="SELECT * from registration";

$result=mysqli_query($data,$sql);

    $info = $result->fetch_assoc();



?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>

    <?php
include 'admin_css.php';
    ?>
    
</head>
<body>

<?php

include 'admin_sidebar.php';

?>

    <div class="content">
        
        <center>
        <h1>Applied For Registration</h1>


        <table border="1px">
            <tr>
                <th style="padding: 20px; font-size: 15px;">Name</th>
                <th style="padding: 20px; font-size: 15px;">Email</th>
                <th style="padding: 20px; font-size: 15px;">phone</th>
                <th style="padding: 20px; font-size: 15px;">ID Number</th>
                <th style="padding: 20px; font-size: 15px;">Store Number</th>
                <th style="padding: 20px; font-size: 15px;">Photo</th>
                <th style="padding: 20px; font-size: 15px;">Document</th>
            </tr>

            <?php
            while($info=$result->fetch_assoc())
            {


            ?>
            <tr>
                <td style="padding: 20px;">
                    <?php echo "{$info['name']}"; ?>
                </td>
                <td style="padding: 20px;">
                    <?php echo "{$info['email']}"; ?>
                </td>
                <td style="padding: 20px;">
                    <?php echo "{$info['phone']}"; ?>
                </td>
                                <td style="padding: 20px;">
                    <?php echo "{$info['id_number']}"; ?>
                </td>
                                <td style="padding: 20px;">
                    <?php echo "{$info['store_number']}"; ?>
                </td>
                                <td style="padding: 20px;">
                    <?php echo "{$info['photo_path']}";{ ?>
                            <a href="<?php echo htmlspecialchars($info['photo_path']); ?>" target="_blank">View Photo</a>
                        <?php }  ?>
                </td>
                                <td style="padding: 20px;">
                    <?php echo "{$info['document_path']}";{ ?>
                            <a href="<?php echo htmlspecialchars($info['document_path']); ?>" target="_blank">View Document</a>
                        <?php }   ?>
                </td>
                
                
            </tr>
            <?php


    }

    ?>

        </table>
        </center>
        


    </div>



     
</body>
</html>