<?php

require_once("basicchecks.php");
require_once("defaultNavbar.php");

require_once("classTeacher.php");

$teacher=new Teacher();


echo "<h2> Select the class: </h2>";

$classList=$teacher->getClasses();


if(empty($classList))
        echo "You have not been assigned to any class. <a href='homepageTeacher.php'> Go Back </a>";
else
    echo $classList;


?>


<?php
require_once("defaultFooter.php")
?>