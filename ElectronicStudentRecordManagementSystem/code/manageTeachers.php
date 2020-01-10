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

    $("#saveButton").click(function() {
      // Fill the modal with the student information

      // var name = $("#teacherName").text();
      // var surname = $("#teacherSurname").text();
      var ssn = $("#teacherSSN").text();
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
      $.post("manageTeacherBackEnd.php", {
          event: "add",
          codFisc: ssn,
          name: name,
          surname: surname,
          principal: principal
        },
        function(data, status) {
          if (data == 1) {
            alert("");
          } else {
            alert("Sorry something has gone wrong, try later.");
          }
          // alert(data);
        });

      // Dismiss manually the modal window
      $('#modal').modal('hide');


    });

    $("#modify").click(function() {
      // Fill the modal with the student information

      // var name = $("#teacherName").text();
      // var surname = $("#teacherSurname").text();
      var ssn = $("#teacherSSN").text();
      var classID = $("#modal-Class").text();
      var selectedSubject = $("#selectSubject").val();

      // INSERISCI NEL DB LA NOTA
      $.post("manageTeacherBackEnd.php", {
          event: "add",
          codFisc: ssn,
          name: name,
          surname: surname,
          principal: principal
        },
        function(data, status) {
          if (data == 1) {
            alert("");
          } else {
            alert("Sorry something has gone wrong, try later.");
          }
          // alert(data);
        });

      // Dismiss manually the modal window
      $('#modal').modal('hide');


    });

    $("#cancelButton").click(function() {

      var ssn = $("#teacherSSN").text();
      var classID = $("#modal-Class").text();
      var note = $("#note").val();
      var hour = $("#hour").val();
      var selectedSubject = $("#selectSubject").val();
      //RIMUOVI LA NOTA

      $.post("manageTeacherBackEnd.php", {
          event: "remove",
          codFisc: ssn,
          name: name,
          surname: surname,
          principal: principal

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
    var teachName = obj.getAttribute("data-name");
    var teachSurname = obj.getAttribute("data-surname");
    var teachSSN = obj.getAttribute("data-ssn");
    var teachPrincipal = obj.getAttribute("data-princ");
    var isPrincipal;
    if(teachPrincipal == "PRINCIPAL"){
        isPrincipal = true;
    }
    else isPrincipal = false;

    // Fill the modal with the student information
    document.getElementById("teacherName").value = teachName;
    document.getElementById("teacherSurname").value = teachSurname;
    document.getElementById("teacherSSN").value = teachSSN;
    document.getElementById("teacherPrincipal").checked = !isPrincipal;
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
            //alert(ssn + " has been correctly deleted.")
            document.getElementById("answer").innerHTML = document.getElementById("answer").innerHTML + '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Success!'+ ssn + ' has been correctly deleted.</strong></div>';

          } else {
            document.getElementById("answer").innerHTML = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong><span class="glyphicon glyphicon-send"></span> Sorry, you cannot delete this element. </strong></div>';
          }
        }) ;
    }
    function addClassSubjectFunction(obj, ssn){
        //console.log(table.children[0]);
        var table = document.getElementById("classSubjectTable");
        var selectedClass = document.getElementById("selectedClass").value;
        var selectedSubject = document.getElementById("selectedSubject").value;
            //console.log("lunghezza tabella " + table.rows.length); 
            //table.rows.length counts header too
            var lastChild = table.rows.length -1;
            var lastRow = table.rows[lastChild];
            var whereToAdd = lastChild;
            table.deleteRow(lastChild);
            newRow = table.insertRow(whereToAdd);
            newRow.id = "tr_"+ whereToAdd;
            var cell0 = newRow.insertCell(0);
            var cell1 = newRow.insertCell(1);
            var cell2 = newRow.insertCell(2);
            cell0.className = "text-center";
            cell0.innerHTML = selectedClass; 

            cell1.className = "text-center";
            cell1.innerHTML = selectedSubject;

            cell2.className = "text-center";
            // onclick=\''+'trashButtonClassSubjectClicked(this,"' + ssn + '","' + selectedClass + '","'+ selectedSubject + '")\''+' dopo lg" e prima della chiusura >
            cell2.innerHTML = '<button type="button" id="trashButtonClassSubject_' + whereToAdd +  '" class="btn btn-danger btn-lg" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
            cell2.addEventListener("click",trashButtonClassSubjectClicked("this", ssn, selectedClass, selectedSubject) );
            table.append(lastRow);
        $.post("manageTeacherBackEnd.php", {
          event: "addClassSubjectPost",
          codFisc: ssn,
          class: selectedClass,
          subject: selectedSubject
        },
        function(data, status) {
          if (data == 1) {
              
            //var oldID = parseInt(document.getElementById("thPlus").innerHTML);
            //document.getElementById("thPlus").innerHTML = oldID + 1;

          } else {

          }
        });
    }
    function trashButtonClassSubjectClicked(obj, ssn, classID, subject){
        //TO BE IMPLEMENTED
    }
</script>


<?php
    echo '<div id="answer"> </div>';

    $teachers = $db->getTeachers();
    echo <<<_TABLEHEAD
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">SSN</th>
                <th scope="col">NAME</th>
                <th scope="col">SURNAME</th>
                <th scope="col">PRINCIPAL</th>
            </tr>
        </thead>
        <tbody>
_TABLEHEAD;
$i = 1;
    foreach($teachers as $teacher){
    if($teacher['principal'] == 1){
        $princ = 'PRINCIPAL';
    }
    else $princ = '';
    $ssn = $teacher['codFisc'];
    $name = $teacher['name'];
    $surname = $teacher['surname'];
    echo <<<_ROWS
                <tr>
                    <th scope="row" class="text-center">$i</th>
                    <td class="text-center">$ssn</td>
                    <td class="text-center">$name</td>
                    <td class="text-center">$surname</td>
                    <td class="text-center">$princ</td>
                    <td class="text-center"><button type="button" id="entranceButton$i" class="btn btn-default btn-lg" data-toggle="modal" data-target="#modal" data-name="$name" data-surname="$surname" data-ssn="$ssn" data-princ="$princ" onclick="fillModalFieldsENTRANCE(this)"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button> </td>
                    <td class="text-center"><button type="button" id="trashButton$i" class="btn btn-danger btn-lg" onclick='trashButtonClicked(this,"$ssn")'><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button> </td>
                </tr>
_ROWS;
    $i++;
    }
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
                          <form class="form-horizontal attendanceForm">
                            <div class="form-group text-center">
                                <label class="control-label text-center">SSN:</label>
                                 <div>
                                    <input type="text" class="form-control manageTeachers text-center" id="teacherSSN">
                                </div>
                            </div>
                            <div class="form-group text-center">
                                  <label control-label text-center">Name:</label>
                                  <div>
                                      <input type="text" class="form-control manageTeachers text-center" id="teacherName">
                                  </div>
                            </div>
                            <div class="form-group text-center">
                                  <label class="control-label text-center">Surname:</label>
                                  <div>
                                      <input type="text" class="form-control manageTeachers text-center" id="teacherSurname">
                                  </div>
                            </div>
                            <table id="classSubjectTable" class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">CLASS</th>
                                            <th scope="col">SUBJECT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
_MODAL;
                        $rowsClassSubject = $db->getClassSubject($ssn);    
                        $j=1; 
                        foreach($rowsClassSubject as $row){
                            $class = $row['classID'];
                            $subject = $row['subject'];
                            echo <<<_CLASS_SUBJECT
                                        <tr id="tr_$j">
                                            <td class="text-center">$class</td>
                                            <td class="text-center">$subject</td>
                                            <td class="text-center"><button type="button" id="trashButtonClassSubject_$j" class="btn btn-danger btn-lg" onclick='trashButtonClassSubjectClicked(this,"$ssn","$class","$subject")'><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button> </td>
                                        </tr>
_CLASS_SUBJECT;
                            $j++;
                            }
                            echo <<<_LASTROW
                            <tr id="rowPlus">
                            <td class='text-center'><select id='selectedClass'>
_LASTROW;
                            $classes = $db->getClasses();    
                            foreach($classes as $c){
                                $c = $c['classID'];
                                echo "<option value=$c>$c</option>";
                            }
                            echo"</select> </td>";

                            $subjects = $db->getSubjects();  
                            echo"<td class='text-center'><select id='selectedSubject'>";
                            foreach($subjects as $s){
                                $s = $s['name'];
                                echo "<option value=$s>$s</option>";
                            }
                            // HO il dubbio che $ssn passato alla funzione addClassSubjectFunction non sia quello del professore di cui si è cliccato il tasto "modifica", ma semplicemente l'ultimo professore stampato dal foreach
                            echo <<<_ENDMODAL
                            </select></td>
                            
                            <td class="text-center"><button type="button" id="addClassSubject" class="btn btn-success btn-lg" onclick='addClassSubjectFunction(this, "$ssn")'><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button> </td>
                            </tr>

                            </tbody>
                            </table>

                            <div class="form-group text-center">
                              <label class="control-label text-center">Principal:</label>
                              <div>
                                <label class="switch"><input type="checkbox" id="teacherPrincipal"><span class="slider round"></span></label>
                            </div>
                          </div>
                          </form>
                      </div>
                      <div class="modal-footer">
                          <button type="button" id="cancelButton" class="btn btn-danger col-xs-3 col-md-offset-3">Cancel</button>
                          <button type="button" id="saveButton" class="btn btn-primary col-xs-3" style="margin-top: 0px">Save changes</button>
                          </div>
                  </div>
              </div>
          </div>
_ENDMODAL;


require_once("defaultFooter.php");

?>