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
                    <li><a href="homepageTeacher.php">Home</a></li>
					
                    <li role="presentation" class="dropdown">

                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            General <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="provideParentMeetingSlots.php"> Parent meetings </a>
                            </li>
                            <li class="divider"></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="seeTimetableTeacher.php">Timetables</a>
                            </li>
                            <li class="divider"></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="changePassword.php">Change Password</a>
                            </li>
                        </ul>
                    </li>					
					
                    <li role="presentation" class="dropdown">

                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Classes <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="recordLesson.php"> New lecture </a>
                            </li>
                            <li class="divider"></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="viewAllLessonTopics.php">All lectures</a>
                            </li>
                            <li class="divider"></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="recordAssignments.php">New assignment</a>
                            </li>
                            <li class="divider"></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="viewAllAssignments.php">All assignments</a>
                            </li>
							<li class="divider"></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="publishSupportMaterial.php">Publish material</a>
                            </li>
                        </ul>
                    </li>						
					
                    <li role="presentation" class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Marks <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="submitMarks.php">Add marks</a>
                            </li>
                            <li class="divider"></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="viewAllMarks.php">All marks</a>
                            </li>
							<?php
								require_once "db.php";
								$db = new dbTeacher();
								if (isset($_SESSION["comboClass"])) {
									$coordinator = $db->isCoordinator($_SESSION["user"], $_SESSION["comboClass"]);
									if ($coordinator) {
										echo "<li class='divider'></li><li class='nav-item dropdown'><a class='dropdown-item' href='publishFinalGrade.php'>Final grades</a></li>";
									}
								}
							?>
                        </ul>
					</li>
		            
					<li><a href="attendance.php">Attendance</a></li>
                    <li><a href="writeStudentNote.php">Discplinar note</a></li>
					

                </ul>
                <ul class="nav navbar-nav navbar-right">

                    <?php
                    if (isset($_SESSION['classes'])) {

                        if (count($_SESSION['classes']) == 1) {
                            //One class
                            echo "
                                <li><a href='#'>" . $_SESSION['comboClass'] . "</a></li>";
                        } else {
                            //More than one class
                            if (isset($_SESSION['comboClass'])) {
                                echo '<li role="presentation" class="dropdown">
									<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'
                                    . $_SESSION["comboClass"] . '<span class="caret"></span> </a>
									<ul class="dropdown-menu">';
                                $i = 0;
                                foreach ($_SESSION['classes'] as $class) {

                                    if (!(($class == $_SESSION['comboClass']))) {
                                        echo <<<_ROW
										<li class="nav-item dropdown">
										<a class="dropdown-item" href="chooseClass.php?classIndex=$i">$class</a>
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