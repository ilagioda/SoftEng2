<?php
require_once("basicChecks.php");

//var_dump($_SESSION);

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {
  $loggedin = true;
}
if (!$loggedin) {
  //require_once("defaultNavbar.php");
  header("Location: login.php");
} else {
  require_once("loggedAdminNavbar.php");
}

require_once("db.php");
$db = new dbAdmin();





?>
<script>
  $(document).ready(function() {
    $("#cancelButton").click(function() {


      // Dismiss manually the modal window
      $('#modal').modal('hide');
    });
  });


  function fillModalFieldsENTRANCE(obj) {

    // Retrieve and store the button id from which this function has been called 
    buttonID = obj.id;

    // Retrieve the information about student in order to show it in the modal window
    var teachName = obj.getAttribute("data-name");
    var teachSurname = obj.getAttribute("data-surname");
    var teachSSN = obj.getAttribute("data-ssn");
    var teachPrincipal = obj.getAttribute("data-princ");
    var isPrincipal;
    if (teachPrincipal == "PRINCIPAL") {
      isPrincipal = true;
    } else isPrincipal = false;

    document.getElementById("answerModal").innerHTML = "";
    // Fill the modal with the student information
    document.getElementById("teacherName").value = teachName;
    document.getElementById("teacherSurname").value = teachSurname;
    document.getElementById("teacherSSN").value = teachSSN;
    document.getElementById("teacherPrincipal").checked = !isPrincipal;

    $.post("manageTeacherBackEnd.php", {
        event: "insertTable",
        codFisc: teachSSN
      },
      function(data, status) {
        if (data == 0) {
          document.getElementById("tableModal").innerHTML = "Sorry. Something wrong has happened.";
        } else {
          document.getElementById("tableModal").innerHTML = data;
        }
      });
  }

  function trashButtonClicked(obj, ssn) {

    // Retrieve and store the button id from which this function has been called 
    buttonID = obj.id;
    $.post("manageTeacherBackEnd.php", {
        event: "delete",
        codFisc: ssn
      },
      function(data, status) {
        if (data == 1) {
          // alert(data);
          //sessionStorage.setItem("success", "true");
          //sessionStorage.setItem("ssnAnswer", ssn);  
          //window.location.replace('manageTeachers.php?success=true&ssn='+ssn);
          var row = document.getElementById("row_" + ssn);
          row.parentNode.removeChild(row);
          //var table = document.getElementById("tableTeachers");
          //table.deleteRow(i);
          document.getElementById("answer").innerHTML = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span>  Success! ' + ssn + ' has been correctly deleted.</strong></div>';
        } else {
          // alert(data);
          document.getElementById("answer").innerHTML = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Sorry, you cannot delete this element. </strong></div>';
        }
      });
  }

  function addClassSubjectFunction(obj, ssn, classID, subject) {
    var selectedClass = document.getElementById("selectedClass").value;
    var selectedSubject = document.getElementById("selectedSubject").value;

    $.post("manageTeacherBackEnd.php", {
        event: "addClassSubjectToATeacher",
        codFisc: ssn,
        class: selectedClass,
        subject: selectedSubject
      },
      function(data, status) {
        if (data == 1) {
          var table = document.getElementById("classSubjectTable");
          var tbody = document.getElementById("tbodyClassSubject");

          //console.log("lunghezza tabella " + table.rows.length); 
          //table.rows.length counts header too
          var lastChildIndex = table.rows.length - 1;
          var lastRow = table.rows[lastChildIndex];
          var whereToAdd = lastChildIndex;
          table.deleteRow(lastChildIndex);
          newRow = table.insertRow(whereToAdd);
          newRow.id = "tr_" + selectedClass + "_" + selectedSubject;
          var cell0 = newRow.insertCell(0);
          var cell1 = newRow.insertCell(1);
          var cell2 = newRow.insertCell(2);
          cell0.className = "text-center";
          cell0.innerHTML = selectedClass;

          cell1.className = "text-center";
          cell1.innerHTML = selectedSubject;

          cell2.className = "text-center";
          // onclick=\''+'trashButtonClassSubjectClicked(this,"' + ssn + '","' + selectedClass + '","'+ selectedSubject + '")\''+' dopo lg" e prima della chiusura >
          cell2.innerHTML = '<button type="button" id="trashButtonClassSubject_' + whereToAdd + '" class="btn btn-danger btn-lg" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
          document.getElementById('trashButtonClassSubject_' + whereToAdd).addEventListener("click", function() {
            trashButtonClassSubjectClicked("this", ssn, selectedClass, selectedSubject);
          });
          tbody.append(lastRow);
          document.getElementById("answerModal").innerHTML = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span>  Success! Tuple has been correctly added.</strong></div>';

        } else {
          document.getElementById("answerModal").innerHTML = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Sorry, you cannot add this element. </strong></div>';
        }
      });
  }
  function addTeacher(obj){
    ssn = document.getElementById("newTeacherSSN").value;
    name = document.getElementById("newTeacherName").value;
    surname = document.getElementById("newTeacherSurname").value;

    $.post("manageTeacherBackEnd.php", {
        event: "add",
        codFisc: ssn,
        name: name,
        surname: surname
      },
      function(data, status) {
        if (data == 1) {
          var table = document.getElementById("tableTeachers");
          var tbody = document.getElementById("tbodyTeachers");
          //console.log("lunghezza tabella " + table.rows.length); 
          //table.rows.length counts header too
          var lastChildIndex = table.rows.length - 1;
          var lastRow = table.rows[lastChildIndex];
          var whereToAdd = lastChildIndex;
          table.deleteRow(lastChildIndex);
          newRow = table.insertRow(whereToAdd);
          newRow.id = "row_" + ssn;

          var cell0 = newRow.insertCell(0); //SSN
          var cell1 = newRow.insertCell(1); //name
          var cell2 = newRow.insertCell(2); //surname
          var cell3 = newRow.insertCell(3); //principal
          var cell4 = newRow.insertCell(4); //modify
          var cell5 = newRow.insertCell(5); //trash
          cell0.className = "text-center";
          cell0.innerHTML = ssn;

          cell1.className = "text-center";
          cell1.innerHTML = name;

          cell2.className = "text-center";
          cell2.innerHTML = surname;

          cell3.className = "text-center";
          cell3.innerHTML = "";

          cell4.className = "text-center";
          // onclick=\''+'trashButtonClassSubjectClicked(this,"' + ssn + '","' + selectedClass + '","'+ selectedSubject + '")\''+' dopo lg" e prima della chiusura >
          cell4.innerHTML = '<td class="text-center"><button type="button" id="entranceButton_' + ssn+'" class="btn btn-default btn-lg" data-toggle="modal" data-target="#modal" data-name="'+ name+'" data-surname="'+surname+'" + data-ssn="' + ssn+'" data-princ="" onclick="fillModalFieldsENTRANCE(this)"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button> </td>';
          
          cell5.className= "text-center";
          cell5.innerHTML = '<td class="text-center"><button type="button" id="trashButton_' + ssn+'" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button> </td>';
          document.getElementById('trashButton_' + ssn).addEventListener("click", function() {
            trashButtonClicked("this", ssn);
          });
          tbody.append(lastRow);
          document.getElementById("newTeacherSSN").value = "";
          document.getElementById("newTeacherName").value = "";
          document.getElementById("newTeacherSurname").value = "";
          document.getElementById("answer").innerHTML = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span>  Success! Teacher has been correctly added.</strong></div>';
        } else {
          // alert(data);
          document.getElementById("answer").innerHTML = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Sorry, this element cannot be added. </strong></div>';
        }
      });
    
  }

  function trashButtonClassSubjectClicked(obj, ssn, classID, subject) {
    // Retrieve and store the button id from which this function has been called 
    buttonID = obj.id;
    $.post("manageTeacherBackEnd.php", {
        event: "deleteClassSubjectForATeacher",
        codFisc: ssn,
        classID: classID,
        subject: subject
      },
      function(data, status) {
        //TO BE IMPLEMENTED
        // Front end modification:
        // the table should be adjusted.
        if (data == 1) {
          //alert(ssn + " has been correctly deleted.")
          var row = document.getElementById("tr_" + classID + "_" + subject);
          row.parentNode.removeChild(row);
          document.getElementById("answerModal").innerHTML = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span>  Success! ' + ssn + ' has been correctly deleted.</strong></div>';
        } else {
          document.getElementById("answerModal").innerHTML = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Sorry, you cannot delete this element. </strong></div>';
        }
      });
  }
</script>


<?php
if (isset($_POST["teacherSSN"]) && isset($_POST["teacherName"]) && isset($_POST["teacherSurname"])) {

  $red = false;
  if (isset($_POST["teacherPrincipal"])) {
    $red = true;
  }

  echo $_POST['teacherSSN'] . " " . $_POST['teacherName'] . " " . $_POST['teacherSurname'] . " " . $_POST['teacherPrincipal'];
  $teacherSSN = $_POST['teacherSSN'];
  $teacherName = $_POST['teacherName'];
  $teacherSurname =  $_POST['teacherSurname'];

  $db->updateTeacherMasterData($teacherSSN, $teacherName, $teacherSurname, $red);
}
echo '<div id="answer"> </div>';

$teachers = $db->getTeachers();
echo <<<_TABLEHEAD
    <table id="tableTeachers" class="table table-hover">
        <thead>
            <tr>
                <th scope="col">SSN</th>
                <th scope="col">NAME</th>
                <th scope="col">SURNAME</th>
                <th scope="col">PRINCIPAL</th>
            </tr>
        </thead>
        <tbody id="tbodyTeachers">
_TABLEHEAD;
$i = 1;
foreach ($teachers as $teacher) {
  if ($teacher['principal'] == 1) {
    $princ = 'PRINCIPAL';
  } else $princ = '';
  $ssn = $teacher['codFisc'];
  $name = $teacher['name'];
  $surname = $teacher['surname'];
  echo <<<_ROWS
                <tr id="row_$ssn">
                    <td class="text-center">$ssn</td>
                    <td class="text-center">$name</td>
                    <td class="text-center">$surname</td>
                    <td class="text-center">$princ</td>
                    <td class="text-center"><button type="button" id="entranceButton_$ssn" class="btn btn-default btn-lg" data-toggle="modal" data-target="#modal" data-name="$name" data-surname="$surname" data-ssn="$ssn" data-princ="$princ" onclick="fillModalFieldsENTRANCE(this)"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button> </td>
                    <td class="text-center"><button type="button" id="trashButton_$ssn" class="btn btn-danger btn-lg" onclick='trashButtonClicked(this,"$ssn")'><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button> </td>
                </tr>
_ROWS;
  $i++;
}

echo <<<_PLUSROW
<tr id="row_plusRow">
    <td class="text-center"><input type="text" id="newTeacherSSN" class="text-center"/></td>
    <td class="text-center"><input type="text" id="newTeacherName" class="text-center"/></td>
    <td class="text-center"><input type="text" id="newTeacherSurname" class="text-center"/></td>
    <td class="text-center"></td>
    <td class="text-center"></td>
    <td class="text-center"><button type="button" id="addTeacherId" class="btn btn-success btn-lg" onclick='addTeacher(this)'><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button> </td>
</tr>

_PLUSROW;

echo "</tbody>
    </table>";

echo <<<_MODAL
          <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myEntrancelabel">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myEntrancelabel">Manage Teacher Master Data</h4>
                      </div>
                      <div class="modal-body">
                        <div id="answerModal"> </div>
                          <form class="form-horizontal" action="manageTeachers.php" method="POST">
                            <div class="form-group text-center">
                                <label class="control-label text-center">SSN:</label>
                                 <div>
                                    <input type="text" class="form-control manageTeachers text-center" name="teacherSSN" id="teacherSSN">
                                </div>
                            </div>
                            <div class="form-group text-center">
                                  <label control-label text-center">Name:</label>
                                  <div>
                                      <input type="text" class="form-control manageTeachers text-center" name="teacherName" id="teacherName">
                                  </div>
                            </div>
                            <div class="form-group text-center">
                                  <label class="control-label text-center">Surname:</label>
                                  <div>
                                      <input type="text" class="form-control manageTeachers text-center" name="teacherSurname" id="teacherSurname">
                                  </div>
                            </div>
                            <div id="tableModal">
                            </div>

                            <div class="form-group text-center">
                              <label class="control-label text-center" for="teacherPrincipal">Principal:</label>
                              <div>
                                <label class="switch"><input type="checkbox" class="form-control" name="teacherPrincipal" id="teacherPrincipal" value="false"><span class="slider round"></span></label>
                            </div>
                          </div>
                         
                      </div>
                      <div class="modal-footer">
                          <button type="button" id="cancelButton" class="btn btn-danger col-xs-3 col-md-offset-3">Cancel</button>
                          <button type="Submit" id="saveButton" class="btn btn-primary col-xs-3" style="margin-top: 0px">Save changes</button>
                          </div> 
                      </form>
                  </div>
              </div>
          </div>
_MODAL;


require_once("defaultFooter.php");
?>