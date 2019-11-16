<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

require_once("classTeacher.php");

$teacher=new Teacher();


echo "<h1 align='center'> Select the class: </h1>";

$classList=$teacher->getClasses();

if(empty($classList))
        echo "You have not been assigned to any class. <a href='homepageTeacher.php'> Go Back </a>";
else{
    echo "<div class='container-fluid text-center'>";
    echo "<table class='table-borderless'><tr>";
    echo $classList;
    echo "</tr></table>";
    echo "</div>";
}



?>


<?php
require_once("defaultFooter.php")
?>