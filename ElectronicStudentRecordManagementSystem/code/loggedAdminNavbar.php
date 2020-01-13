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
                    <li><a href="homepageAdmin.php">Home</a></li>
					
                    <li><a href="enrollstudent.php">Enroll student</a>
					
                    <li><a href="mailInterface.php">Access parent</a>
					
					<li role="presentation" class="dropdown">

					<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						Classes <span class="caret"></span>
					</a>

					<ul class="dropdown-menu">
						<li class="nav-item dropdown">
							<a class="dropdown-item" href="classComposition.php">Class composition</a>
						</li>
						<li class="divider"></li>
						<li class="nav-item dropdown">
							<a class="dropdown-item" href="publishTimetable.php">Publish timetables</a>
						</li>
					</ul>
					</li>
					
					<li role="presentation" class="dropdown">

					<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						Management <span class="caret"></span>
					</a>

					<ul class="dropdown-menu">
					<?php
						if ($_SESSION['sysAdmin'] == 1) {
							echo '<li class="nav-item dropdown"><a class="dropdown-item" href="setupAccounts.php">Setup official accounts</a></li><li class="divider"></li>';
						}
					?>
						<li class="nav-item dropdown">
							<a class="dropdown-item" href="manageTeachers.php">Manage teachers</a>
						</li>
						<li class="divider"></li>
						<li class="nav-item dropdown">
							<a class="dropdown-item" href="changePassword.php">Change password</a>
						</li>
					</ul>
					</li>
					
				<li role="presentation" class="dropdown">

				<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
					Communications <span class="caret"></span>
				</a>

				<ul class="dropdown-menu">
					<li class="nav-item dropdown">
						<a class="dropdown-item" href="publishInternalCommunications.php">Internal</a>
					</li>
					<li class="divider"></li>
					<li class="nav-item dropdown">
						<a class="dropdown-item" href="publishCommunications.php">General</a>
					</li>
				</ul>
				</li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">