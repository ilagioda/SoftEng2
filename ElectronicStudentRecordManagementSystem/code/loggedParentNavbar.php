<body>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><img class="logo" src="images/logo.png" alt="logo"></a>
            </div>



            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="homepageParent.php">Home</a></li>
                    <li role="presentation" class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Class <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li class="nav-item dropdown"><a class="dropdown-item" href="viewChildAssignment.php">Assignments</a></li>
                            <li class="nav-item dropdown"><a class="dropdown-item" href="viewStudentNote.php">Discplinar Notes</a></li>
                            <li class="nav-item dropdown"><a class="dropdown-item" href="viewChildLessonTopics.php">Lectures</a></li>
                            <li class="nav-item dropdown"><a class="dropdown-item" href="studentAttendance.php">Attendance</a></li>
                            <li class="nav-item dropdown"><a class="dropdown-item" href="viewSupportMaterial.php">Materials</a></li>
                            <li class="nav-item dropdown"><a class="dropdown-item" href="seeTimetable.php">Timetable</a></li>
                        </ul>
					</li>
                    <li role="presentation" class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Marks <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li class="nav-item dropdown"><a class="dropdown-item" href="viewMarks.php">View marks</a></li>
                            <li class="nav-item dropdown"><a class="dropdown-item" href="viewFinalGrades.php">Final grades</a></li>
                        </ul>
					</li>
					
                    <li><a href="selectTeacherForBooking.php">Parent Meetings</a></li>
                    <li role="presentation" class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            General <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li class="nav-item dropdown"><a class="dropdown-item" href="changePassword.php">Change password</a></li>
                        </ul>
					</li>

				</ul>
                <ul class="nav navbar-nav navbar-right">

                    <?php
                    if (isset($_SESSION['children'])) {

                        if (count($_SESSION['children']) == 1) {
                            //One child
                            echo "
                                <li><a href='#'>" . $_SESSION['childName'] . " " . $_SESSION['childSurname'] . "</a></li>";
                        } else {
                            //More than one child
                            if (isset($_SESSION['childName']) && isset($_SESSION['childSurname'])) {

                                echo '<li role="presentation" class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'
                                    . $_SESSION["childName"] . ' ' . $_SESSION["childSurname"] . '<span class="caret"></span> </a>
                                <ul class="dropdown-menu">';
                                $i = 0;
                                foreach ($_SESSION['children'] as $child) {

                                    if (!(($child['name'] == $_SESSION['childName']) && ($child['surname'] == $_SESSION['childSurname']))) {
                                        echo <<<_ROW
                                    <li class="nav-item dropdown">
                                    <a class="dropdown-item" href="chooseChild.php?childIndex=$i">$child[name] $child[surname]</a>
                                    </li>
_ROW;
                                    }
                                    $i++;
                                }
                                echo '</ul>
                                  </li>';
                            }
                        }
                    }
                    ?>

                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">