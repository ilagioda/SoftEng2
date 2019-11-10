<?php

require_once('basicChecks.php');
$_SESSION['user'] = "marcoGay";
$_SESSION['role'] = "admin";

require_once('db.php');

$dbAdmin = new dbAdmin();

if (isset($_POST["view"])) {
  //if set it means that the user requested the composition of a class
  //echo sizeof($_POST);

  foreach ($_POST as $key => $value) {
    if ($key != "view") {
      //echo $value;
      $classComposition = $dbAdmin->readClassCompositions($value);
      //print_r($classComposition);

      require_once("defaultNavbar.php");
      echo <<<REQUESTEDPAGE
              <div class="col-sm-8 text-center">
                <h1>Welcome</h1>
                <p>The purpose of this page is to select the class in order to see and accept the class composition.</p>
                <hr>

                <div class="container text-left">
                  <h2>Classes</h2>
                  <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Fiscal Code</th>
                      <th>Name</th>
                      <th>Surname</th>
                      <th>Class</th>
                    </tr>
                  </thead>
                    <tbody>
REQUESTEDPAGE;

      foreach ($classComposition as $value) {
        echo <<< ROW
                            <tr>
                            <td>$value[0]</td>
                            <td>$value[1]</td>
                            <td>$value[2]</td>
                            <td>$value[3]</td>
                            </tr>
ROW;
      }
      $valueString = json_encode($classComposition);

      echo <<<ENDOFREQUESTEDPAGE
                    <tr>
                    <td>
                    <div class="form-group">
                    <form class="form-horizontal" method="post" action="./classComposition.php">
                      <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-default pull-left">Cancel</button>
                      </div>
                    </form>
                    </div>
                    </td>
                    <td>
                    </td>
                    <td>
                    <div class="form-group">
                    <form class="form-horizontal" method="post" action="./classComposition.php">
                      <div class="col-sm-offset-2 col-sm-10">
                      <input type="text" name="confirm"  hidden value='$valueString'>
                      <button type="submit" class="btn btn-default pull-right">Confirm</button>
                      </div>
                    </form>
                    </div>
                    </td>
                    <td>
                    </td>
                  </tr>
                  </tbody>                
                  </table>
                </div>
                </div>
ENDOFREQUESTEDPAGE;
      require_once("defaultFooter.php");
    }
  }
} else {
  if (isset($_POST["confirm"])) {
    // The user has accepted the composition of a class
    // The db should be updated
    //print($_POST["confirm"]);



    $parameters = json_decode($_POST["confirm"]);

    $dbAdmin->updateStudentsClass($parameters);
  }

  //print the normal page

  $classes = $dbAdmin->readAllClasses();

  require_once("defaultNavbar.php");
  echo <<<NORMALPAGE
              <div class="col-sm-20 text-left">
                <h1>Welcome</h1>
                <p>The purpose of this page is to select the class in order to see and accept the class composition.</p>
                <hr>
                <div class="container col-sm-10">
                 
NORMALPAGE;
  if (isset($classes)) {
    if (is_array($classes)) {

      echo ' <h2>Classes</h2>
      <table class="table table-hover">
        <tbody>';

      foreach ($classes as $value) {
        echo <<< ROW
                         
                            <tr>
                            <td class ="col-sm-4">$value</td>
                            <td class ="col-sm-1">
                            <form class="form-horizontal" method = "post" action="./classComposition.php">
                              <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <input type="text" name="$value"  hidden value="$value">
                                  <button type="submit" name="view" class="btn btn-default pull-right">View</button>
                                  </div>
                                  </div>
                            </form>
                            </td>
                            </tr>
ROW;
      }
      echo ' </tbody>
      </table>';
    } else {
      echo <<< ROW1
                          <h2>Classes</h2>
                          <table class="table table-hover">
                            <tbody>
                            <tr>
                            <td class ="col-sm-4>$value</td>
                            <td class ="col-sm-1">
                            <form class="form-horizontal" method = "post" action="./classComposition.php">
                              <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <input type="text" name="$value"  hidden value="$value">
                                  <button type="submit" name="view" class="btn btn-default pull-right">View</button>
                                  </div>
                                  </div>
                            </form>
                            </td>
                            </tr>
                            </tbody>
                            </table>
ROW1;
    }
  } else {
    echo "<h2> There is not any proposed class composition.</h2>";
  }

  echo <<<ENDOFNORMALPAGE
              </div>
              </div>
            
ENDOFNORMALPAGE;
  require_once("defaultFooter.php");
}
?>
