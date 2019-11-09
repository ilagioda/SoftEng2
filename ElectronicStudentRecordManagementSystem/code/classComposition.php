<?php
require_once('basicChecks.php');
require_once('db.php');
$dbAdmin = new dbAdmin();

if (isset($_POST["view"])) {
  //if set it means that the user requested the composition of a class

  //echo sizeof($_POST);

  foreach ($_POST as $key => $value) {
    if ($key != "view") {
      //
      echo $value;


      echo <<<REQUESTEDPAGE
        <body>
          <nav class="navbar navbar-inverse">
            <div class="container-fluid">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><img class="logo" src=images/logo.png> </a> </div> <div class="collapse navbar-collapse" id="myNavbar">
                  <ul class="nav navbar-nav">
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Projects</a></li>
                    <li><a href="#">Contact</a></li>
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                  </ul>
              </div>
            </div>
          </nav>

          <div class="container-fluid text-center">
            <div class="row content">
              <div class="col-sm-2 sidenav">
                <p><a href="#">Link</a></p>
                <p><a href="#">Link</a></p>
                <p><a href="#">Link</a></p>
              </div>
              <div class="col-sm-8 text-left">
                <h1>Welcome</h1>
                <p>The purpose of this page is to select the class in order to see and accept the class composition.</p>
                <hr>
                <div class="container">
                  <h2>Classes</h2>
                  <table class="table table-hover">
                    <tbody>
REQUESTEDPAGE;

      foreach ($classes as $value) {
        echo <<< ROW
                            <tr>
                            <td>$value</td>
                            <td>
                            <form method="post" class="form-horizontal" action="./classComposition.php">
                              <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <input type="text" name="$value"  hidden value="$value">
                                  <button type="submit" name="view" class="btn btn-default">View</button>
                            </form>
                            </td>
                            </tr>
ROW;
      }
      echo <<<ENDOFREQUESTEDPAGE
                    </tbody>
                  </table>
                </div>
              </div>
            </div>




            <footer class="container-fluid text-center">
              <p>Footer Text</p>
            </footer>

        </body>

        </html>
ENDOFREQUESTEDPAGE;
    }
  }
} else {
//print the normal page
  
  $classes = $dbAdmin->readAllClasses();
  
  echo <<<NORMALPAGE
        <body>
          <nav class="navbar navbar-inverse">
            <div class="container-fluid">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><img class="logo" src=images/logo.png> </a> </div> <div class="collapse navbar-collapse" id="myNavbar">
                  <ul class="nav navbar-nav">
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Projects</a></li>
                    <li><a href="#">Contact</a></li>
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                  </ul>
              </div>
            </div>
          </nav>

          <div class="container-fluid text-center">
            <div class="row content">
              <div class="col-sm-2 sidenav">
                <p><a href="#">Link</a></p>
                <p><a href="#">Link</a></p>
                <p><a href="#">Link</a></p>
              </div>
              <div class="col-sm-8 text-left">
                <h1>Welcome</h1>
                <p>The purpose of this page is to select the class in order to see and accept the class composition.</p>
                <hr>
                <div class="container">
                  <h2>Classes</h2>
                  <table class="table table-hover">
                    <tbody>
NORMALPAGE;

  foreach ($classes as $value) {
    echo <<< ROW
                            <tr>
                            <td>$value</td>
                            <td>
                            <form method="post" class="form-horizontal" action="./classComposition.php">
                              <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <input type="text" name="$value"  hidden value="$value">
                                  <button type="submit" name="view" class="btn btn-default">View</button>
                            </form>
                            </td>
                            </tr>
ROW;
  }
  echo <<<ENDOFNORMALPAGE
                    </tbody>
                  </table>
                </div>
              </div>
            </div>




            <footer class="container-fluid text-center">
              <p>Footer Text</p>
            </footer>

        </body>

        </html>
ENDOFNORMALPAGE;
}
?>



<!-- <table class="table table-hover">
            <thead>
              <tr>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Class</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>John</td>
                <td>Doe</td>
                <td>1A</td>
                <td>
                  <form method="post" class="form-horizontal" action="./classComposition.php">
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default" name>View</button>
                  </form>
                </td>
              </tr>
              <tr>
                <td>Mary</td>
                <td>Moe</td>
                <td>1A</td>
                <td>
                  <form class="form-horizontal" action="./classComposition.php">
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">View</button>
                  </form>
                </td>
              </tr>
              <tr>
            </tbody> */
            -->