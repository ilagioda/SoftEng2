<?php // Example 26-1: functions.php
$dbhost  = 'localhost';    // Unlikely to require changing---localhost
$dbname  = 'MY_DATABASE';   // Modify these...---s256700
$dbuser  = 'root';   // ...variables according ---s256700
$dbpass  = '';   // ...to your installation --- arinquai

$R = 10;
$C = 6;


$connection =  mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if ($connection->connect_error) die("Fatal Error2");

function createTable($name, $query)
{
    //echo "name : $name";
    //echo "query: $query";
    //echo "<br>";
    $res = queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    //CREATE TABLE IF NOT EXISTS users(user VARCHAR(16),pass VARCHAR(16));
    //echo "Table '$name' created or already exists.<br>";
    return $res;
}

function queryMysql($query)
{
    global $connection;
    $result = $connection->query($query);
    //if (!$result) die("Fatal Error3");
    return $result;
}

function destroySession()
{
    $_SESSION=array();
    
    if (session_id() != "" || isset($_COOKIE[session_name()]))
        setcookie(session_name(), '', time()-2592000, '/');
        
        session_destroy();
}

function sanitizeString($var)
{
    global $connection;
    $var = strip_tags($var);
    $var = htmlentities($var);
    if (get_magic_quotes_gpc())
        $var = stripslashes($var);
        return $connection->real_escape_string($var);
}
function printModifiableSeats($R, $C){
    global $connection;
    $message="";
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
    }
    if( isset($user) && isset($_SESSION['seats']) && isset($_SESSION) && $_SESSION['seats'] != "" && isset($_POST) && isset($_POST['Buy'])){
        //var_dump($_POST);
        // try{
        if(!$connection->autocommit(false)){
            header("Location: index.php?$user");
        }
        $checkUser = queryMysql("SELECT * FROM users WHERE user='$user'");
        if($checkUser){
            if($checkUser->num_rows != 0){
                $total = $_SESSION['seats'];
                $flag=0;
                $seats_to_lock = "POSTO = '";
                $req_seats = explode(">", $total);
                
                //var_dump($_SESSION);
                
                //preparing a string to lock only the requested seats
                for($i=0; $i<count($req_seats)-2; $i++){
                    //echo count($req_seats);
                    $seats_to_lock = $seats_to_lock.$req_seats[$i] . "' or POSTO= '";
                }
                $seats_to_lock = $seats_to_lock .$req_seats[count($req_seats)-2]."'";
                //echo "Hai richiesto i posti ".$seats_to_lock ."<br>";
                
                $connection->begin_transaction();
                $result = queryMysql("SELECT * FROM posti WHERE $seats_to_lock FOR UPDATE");
                if($result){
                    foreach($req_seats as $seat){
                        //     if($seat == "") die("Questo posto era vuoto");
                        if($seat != ""){
                            //$result = queryMysql("SELECT * FROM POSTI WHERE POSTO = '$req_seats' FOR UPDATE");
                            $tuple = $result->fetch_array(MYSQLI_ASSOC);
                            //$tuple['PREN'] != $user || $tuple['DISP'] != 2
                            if($tuple['DISP']==0 || ($tuple['DISP']==2 && $tuple['PREN']!=$user) ){ 
                                $message = "Some seats that you chose could have been reserved by another user in the meanwhile";
                                $flag=1;
                                break;
                            }
                        }
                    }
                    if($flag == 0){
                        //Procede with the purchase
                        foreach($req_seats as $seat){
                            if($seat != ""){
                                //echo "flag = 0";
                                $result = queryMysql("UPDATE posti SET DISP=0 WHERE POSTO = '$seat'");
                            }
                        }
                        $_SESSION['seats']="";
                        $message = "YOU CORRECTLY PURCHASED THE SELECTED SEATS ".$user;
                    }
                    else{
                        //free all booked seats by $user
                        foreach($req_seats as $seat){
                            if($seat != ""){
                                //echo "flag = 1";
                                $result = queryMysql("UPDATE posti SET DISP=1 WHERE POSTO = '$seat' AND PREN='$user' ");
                                $_SESSION['seats']=str_replace($seat.">", "", $_SESSION['seats']);
                            }
                        }
                        //$connection->rollback();
                    }
                    $connection->commit();
                    $connection->autocommit(true);
                }///
                else{
                    $connection->rollback();
                    $connection->autocommit(true);
                    $message="Some error occurred. Please retry";
                }
            }
            else{
                $message="User not existing";
            }
        }
        else{
            
            $message = "Some error occurred. Please retry";
        }
        
        //////
    }
    
    $sts = $_SESSION['seats'];
    echo <<<_TEXT
    <form action="index.php" method="post" onsubmit="return validateBuy($sts)">
_TEXT;
    echo '<div><br><span id="notes"></span></div><br><br>';
    echo '<table>';
    
    echo "<tr><td> &nbsp </td> ";
    for($i=0, $lettera = 'A'; $i < $C ; $lettera++, $i++){
        echo "<td> $lettera </td> ";
        if($i == 2) echo "<td>&nbsp&nbsp&nbsp&nbsp</td>";
    }
    echo "</tr>";
    $free=0;
    $purch=0;
    $booked=0;
    $_SESSION['seats']="";
    $posti = queryMysql("SELECT * FROM posti");
    if(!$posti) die ("Website unavailable");
    for($i = 1, $numero=0; $i <= $R; $i++, $numero++){
        echo "<tr><td>". $i ."</td>";
        for($j = 1, $lettera = 'A'; $j <= $C ; $j++, $lettera++){
            $obj_posto = $posti->fetch_array(MYSQLI_ASSOC);
            $disp = $obj_posto["DISP"];
            //echo 'disp vale: '.$disp;
            $id = $obj_posto["POSTO"];
            $tab_user = $obj_posto["PREN"];
            //echo "id vale ".$id;
            if($disp == 1){
                echo '<td> <button type="button" class="available" id='.$id.' onclick="selection('.$id.')"> </td>';
                $free++;
            }
            else if($disp == 0){
                echo '<td> <button type="button" id='.$id.' class="purchased" onmouseenter="notPurchasable(this)" onmouseleave="removeNotes()"> </td>';
                $purch++;
            }
            else if($tab_user != $user){
                echo '<td> <button type="button" class="reserved_other" id='.$id.' onclick="selection('.$id.')"> </td>';
                $booked++;
            }
            else{
                $_SESSION['seats']=$_SESSION['seats'].$id.">";
                echo <<<_USERSEAT
                  <td>
                  <button type="button" class="reserved" id=$id onclick="selection($id)"> </td>
_USERSEAT;
                $booked++;
            }
            if($j == $C/2) echo '<td>&nbsp&nbsp&nbsp&nbsp</td>';
        }
        echo '</tr>';
    }
    echo '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
    
    echo '<div class="sidenav2"><ul><li> ';
//     echo '<td><input type="submit" name="Buy" value="Buy"/></td></tr>';
    echo '<a><input type="submit" name="Buy" value="Buy"/></a>';
    echo '</li></ul></div>';
    echo "</form>";
    
    echo '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
//     echo '<td><a href="index.php?view='.$user.'"><input type="button" class="but" value="Update"></a></td></tr>';
    echo '</table></div>';
    $total = $R*$C;
    echo '<table id="table_sx">';
    echo '<tr><td>&nbsp&nbsp</td><td>TOTAL SEATS NUMBER:</td><td id="tot"/>'.$total.'</td></tr>';
    echo '<tr><td><img src="greenSquare.jpg" class="my_img">&nbsp</td></td><td>FREE SEATS:</td><td id="free"/>'.$free.'</td></tr>';
    echo '<tr><td><img src="redSquare.jpg" class="my_img">&nbsp</td><td>PURCHASED SEATS:</td><td id="purch"/>'.$purch.'</td></tr>';
    echo '<tr><td><img src="yellowSquare.jpg" class="my_img"><img src="orangeSquare.png" class="my_img"></td><td>BOOKED SEATS:</td><td id="booked"/>'.$booked.'</td></tr>';
    echo "</table>";
    
    if($message != ""){
        echo <<<_MESS
                <script type="text/javascript"><!--
                    document.getElementById("notes").innerHTML = "$message";
                //--></script>
_MESS;
    }
}
function printFixedSeats($R, $C){
    echo '<table>';
    
    echo "<tr><td> &nbsp </td> ";
    for($i=0, $lettera = 'A'; $i < $C ; $lettera++, $i++){
        echo "<td> $lettera </td> ";
        if($i == 2) echo "<td>&nbsp&nbsp&nbsp&nbsp</td>";
    }
    echo "</tr>";
    $free=0;
    $purch=0;
    $booked=0;
    $posti = queryMysql("SELECT * FROM posti");
    for($i = 1, $numero=0; $i <= $R; $i++, $numero++){
        echo "<tr><td>". $i ."</td>";
        for($j = 1, $lettera = 'A'; $j <= $C ; $j++, $lettera++){
            $obj_posto = $posti->fetch_array(MYSQLI_ASSOC);
            $disp = $obj_posto["DISP"];
            //echo 'disp vale: '.$disp;
            $id = $obj_posto["POSTO"];
            //echo "id vale ".$id;
            if($disp == 1){
                echo '<td><button type="button" id='.$id.' class="available" disabled></td>';
                $free++;
            }
            else if($disp == 0){
                echo '<td> <button type="button" class="purchased" disabled> </td>';
                $purch++;
            }
            else{
                echo '<td> <button type="button" class="reserved_other" disabled> </td>';
                $booked++;
            }
            if($j == $C/2) echo '<td>&nbsp&nbsp&nbsp&nbsp</td>';
        }
        echo '</tr>';
    }
    //echo '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
    //echo '<td><input type="button" class="but" value="Update" onClick="update()"/></td></tr>';
    echo '</table>';
    $total = $R*$C;
    echo '<table id="table_sx">';
    echo '<tr><td>&nbsp&nbsp</td><td>TOTAL SEATS NUMBER:</td><td id="tot"/>'.$total.'</td></tr>';
    echo '<tr><td><img src="greenSquare.jpg" class="my_img">&nbsp</td></td><td>FREE SEATS:</td><td id="free"/>'.$free.'</td></tr>';
    echo '<tr><td><img src="redSquare.jpg" class="my_img">&nbsp</td><td>PURCHASED SEATS:</td><td id="purch"/>'.$purch.'</td></tr>';
    echo '<tr><td><img src="yellowSquare.jpg" class="my_img"><img src="orangeSquare.png" class="my_img"></td><td>BOOKED SEATS:</td><td id="booked"/>'.$booked.'</td></tr>';
    echo "</table>";
}

?>
