<?php
require_once("basicChecks.php");

//var_dump($_SESSION);

$loggedin = false;
if (isset($_SESSION['user']) && $_SESSION['role'] == "teacher") {
  $loggedin = true;
}
if (!$loggedin) {
  //require_once("defaultNavbar.php");
  header("Location: login.php");
} else {
	if (!isset($_SESSION['comboClass'])) {
        header("Location: chooseClass.php");
        exit;
    }
  require_once("loggedTeacherNavbar.php");
}
?>
<script>
  $(document).ready(function() {

    $("#saveButton").click(function() {
      // Fill the modal with the student information

      // var name = $("#studentName").text();
      // var surname = $("#studentSurname").text();
      var ssn = $("#studentSsn").text();
      var classID = $("#modal-Class").text();
      var note = $("#note").val();
      var hour = $("#hour").val();
      var selectedSubject = $("#selectSubject").val();

      // alert(name);
      // alert(surname);
      // alert(ssn);
      // alert(classID);
      // alert(note);
      // alert(hour);

      // INSERISCI NEL DB LA NOTA
      $.post("writeStudentNoteBackEnd.php", {
          event: "recordNote",
          codFisc: ssn,
          classID: classID,
          note: note,
          hour: hour,
          subject: selectedSubject
        },
        function(data, status) {
          if (data == 1) {
            alert("Discplinar note registered.");
          } else {
            alert("Sorry something has gone wrong, try later.");
          }
          // alert(data);
        });

      // Dismiss manually the modal window
      $('#modal').modal('hide');


    });

    $("#removeButton").click(function() {

      var ssn = $("#studentSsn").text();
      var classID = $("#modal-Class").text();
      var note = $("#note").val();
      var hour = $("#hour").val();
      var selectedSubject = $("#selectSubject").val();
      //RIMUOVI LA NOTA

      $.post("writeStudentNoteBackEnd.php", {
          event: "removeNote",
          codFisc: ssn,
          subject: selectedSubject
        },
        function(data, status) {
          if (data == 1) {
            alert("All the discplinar notes written by you in the selected subject were removed.");
          } else {
            alert("Sorry something has gone wrong, try later.");
          }
          // alert(data);
        });

      // Dismiss manually the modal window
      $('#modal').modal('hide');
    });
  });


  function fillModalFieldsENTRANCE(obj) {

    // Retrieve and store the button id from which this function has been called 
    buttonID = obj.id;

    // Retrieve the information about student in order to show it in the modal window
    var studName = obj.getAttribute("data-name");
    var studSurname = obj.getAttribute("data-surname");
    var studSSN = obj.getAttribute("data-ssn");
    var studClass = obj.getAttribute("data-c");

    // Fill the modal with the student information
    document.getElementById("studentName").innerHTML = studName;
    document.getElementById("studentSurname").innerHTML = studSurname;
    document.getElementById("studentSsn").innerHTML = studSSN;
    document.getElementById("modal-Class").innerHTML = studClass;

    //delete the previous existing content if there is one.
    $("#note").val("");

    // Fill hours in the modal (hours 1 to 6)
    var select = document.getElementById("hour");
    var child = select.lastElementChild;
    while (child) {
      select.removeChild(child);
      child = select.lastElementChild;
    }

    // not admissible number
    for (let i = 1; i < 7; i++) {
      option = document.createElement("option");
      option.textContent = i;
      select.appendChild(option);
    }
  }
</script>


<?php

  // LA CLASSE E' STATA SELEZIONATA

  require_once("classTeacher.php");

  $teacher = new Teacher();

  $chosenClass = $_SESSION['comboClass'];
  // echo $class;
  echo '<div class="col-md-12 text-center">
  <h2>Insert notes</h2>';

  // Retrieve the student of the selected class
  $students = $teacher->getStudents2($chosenClass);

  $subjects = $teacher->getSubjectByClassAndTeacher($chosenClass);

  // Create the table containing the students
  // Check if the class has at least one student
  if (empty($students)) {
    // The class has no students
    echo "<p>No students in the selected class!</p>";
  } else {
    if (empty($subjects))
      echo "<p> No subjects are teached by the selected teacher!</p>";
    else {
      // The class has at least one student
      echo '<div class="table-responsive">';
      echo '<table class="table table-striped table-bordered text-center" id="attendanceTable">';
      echo '<tr style="color: black; font-size: 20px;"><td><b>Name</b></td><td><b>Surname</b></td><td><b>SSN</b><td><b>InsertNote</b></td></tr>';

      $i = 0;
      foreach ($students as $stud) {
        $fields = explode(",", $stud);
        // $fields[0] --> name
        // $fields[1] --> surname
        // $fields[2] --> SSN
        // coloumns: name, surname, ssn, presence, lateEntrance, earlyExit
        echo <<<_ROW
              <tr>
              <td style="vertical-align: middle;">$fields[0]</td>
              <td style="vertical-align: middle;">$fields[1]</td>
              <td style="vertical-align: middle;">$fields[2]</td>
              <td><button type="button" id="entranceButton$i" class="btn btn-primary" data-toggle="modal" data-target="#modal" data-name="$fields[0]" data-surname="$fields[1]" data-ssn="$fields[2]" data-c="$chosenClass" onclick="fillModalFieldsENTRANCE(this)">
              Note
              </button>
              </td>
_ROW;
        $i++;
      }
      echo "</table>";
      echo "</div>";

      echo <<<_MODAL
          <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myEntrancelabel">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myEntrancelabel">Record student note</h4>
                      </div>
                      <div class="modal-body col-xs-6 col-xs-offset-3">
                          <form class="form-horizontal attendanceForm">
                              <div class="form-group">
                                  <label class="col-xs-6 control-label">Name:</label>
                                  <div class="col-xs-4">
                                      <p class="form-control-static" id="studentName"></p>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label class="col-xs-6 control-label">Surname:</label>
                                  <div class="col-xs-4">
                                      <p class="form-control-static" id="studentSurname"></p>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label class="col-xs-6 control-label">SSN:</label>
                                  <div class="col-xs-4">
                                      <p class="form-control-static" id="studentSsn"></p>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label class="col-xs-6 control-label">Class:</label>
                                  <div class="col-xs-4">
                                      <p class="form-control-static" id="modal-Class"></p>
                                  </div>
                              </div>
                              <div class="form-group bootstrap-select-wrapper">
                                  <label class="col-xs-6 control-label">Hour:</label>
                                  <select id="hour">
                                  </select>
                              </div>
                              <div class="form-group bootstrap-select-wrapper">
                                <label class="col-xs-6 control-label">Subject:</label>
                                <select id="selectSubject" title="Select a subject">
_MODAL;

      foreach ($subjects as $value) {
        echo "<option value=$value>$value</option>";
      }
      echo <<<_MODAL

                                </select>
                              </div>
                              <div class="form-group">
                                <label for="comment">Note:</label>
                                <textarea class="form-control" rows="5" id="note" style="width: 265px; height: 175px;"></textarea>
                              </div> 
                          </form>
                      </div>
                      <div class="modal-footer">
                          <button type="button" id="removeButton" class="btn btn-danger col-xs-3 col-md-offset-3">Remove</button>
                          <button type="button" id="saveButton" class="btn btn-primary col-xs-3" style="margin-top: 0px">Save changes</button>
                      </div>
                  </div>
              </div>
          </div>
_MODAL;
    }
  }


echo "</div>";
require_once("defaultFooter.php");
?>