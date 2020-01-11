<?php
if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {


    require_once("db.php");

    $db = new dbAdmin();



    if (isset($_POST["event"])) {

        if ($_POST["event"] == "delete") {
            //Delete a teacher
            if (isset($_POST["codFisc"])) {
                $ssn = $_POST["codFisc"];
                if ($db->deleteTeacher($ssn))
                    echo 1;
                else echo 0;
                exit;
            }
        } elseif ($_POST["event"] == "deleteClassSubjectForATeacher") {
            //Delete a subject that is teached from a teacher in a particular class
            /*
               codFisc: ssn,
                classID: classID,
                subject: subject
            */
            if (isset($_POST["codFisc"]) && isset($_POST["classID"]) && isset($_POST["subject"])) {

                $ssn = $_POST["codFisc"];
                $classID = $_POST["classID"];
                $subject = $_POST["subject"];

                if ($db->deleteSubjectTeachedInAClassByATeacher($ssn, $classID, $subject))
                    echo 1;
                else echo 0;
                exit;
            }
        } elseif ($_POST["event"] == "insertTable") {
            if (isset($_POST['codFisc'])) {
                $ssn = $_POST['codFisc'];
                $ssn = $db->sanitizeString($ssn);
                echo <<<_MODAL
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
                $j = 1;
                foreach ($rowsClassSubject as $row) {
                    $class = $row['classID'];
                    $subject = $row['subject'];

                    echo '<tr id="tr_'.$class.'_'.$subject.'">';
                                                            
                    echo <<<_CLASS_SUBJECT
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
                foreach ($classes as $c) {
                    $c = $c['classID'];
                    echo "<option value=$c>$c</option>";
                }
                echo "</select> </td>";

                $subjects = $db->getSubjects();
                echo "<td class='text-center'><select id='selectedSubject'>";
                foreach ($subjects as $s) {
                    $s = $s['name'];
                    echo "<option value=$s>$s</option>";
                }
                // HO il dubbio che $ssn passato alla funzione addClassSubjectFunction non sia quello del professore di cui si è cliccato il tasto "modifica", ma semplicemente l'ultimo professore stampato dal foreach
                echo <<<_ENDMODAL
                                                </select></td>
                                                
                                                <td class="text-center"><button type="button" id="addClassSubject" class="btn btn-success btn-lg" onclick='addClassSubjectFunction(this, "$ssn","$c","$s")'><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button> </td>
                                                </tr>
    
                                                </tbody>
                                                </table>
_ENDMODAL;
            } else {
                echo 0;
                exit;
            }
        } elseif ($_POST["event"] == "addClassSubjectToATeacher") {
            /*
                event: "addClassSubjectToATeacher",
                codFisc: ssn,
                class: classID,
                subject: subject
            */
            if (isset($_POST["codFisc"]) && isset($_POST["class"]) && isset($_POST["subject"])) {

                $codFisc = $_POST["codFisc"];
                $classID = $_POST["class"];
                $subject = $_POST["subject"];
                
                if ($db->addSubjectTeachedInAClassByATeacher($codFisc, $classID, $subject))
                    echo 1;
                else echo 0;
                exit;
            }
        }
    }
}
