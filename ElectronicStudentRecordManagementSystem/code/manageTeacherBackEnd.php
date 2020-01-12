<?php
if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['user']) && $_SESSION['role'] == "admin") {


    require_once("db.php");

    $db = new dbAdmin();



    if (isset($_POST["event"])) {

        if ($_POST["event"] == "delete") {
            //Delete a teacher
            if (isset($_POST["codFisc"]) && $_POST["codFisc"] != "") {
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
            if (isset($_POST["codFisc"]) && $_POST["codFisc"] != "" && isset($_POST["classID"]) && $_POST["classID"] != "" && isset($_POST["subject"]) && $_POST["subject"] != "") {

                $ssn = $_POST["codFisc"];
                $classID = $_POST["classID"];
                $subject = $_POST["subject"];

                if ($db->deleteSubjectTeachedInAClassByATeacher($ssn, $classID, $subject))
                    echo 1;
                else echo 0;
                exit;
            }
        } elseif ($_POST["event"] == "insertTable") {
            if (isset($_POST['codFisc']) && $_POST["codFisc"] != "") {
                $ssn = $_POST['codFisc'];
                $ssn = $db->sanitizeString($ssn);


                $rowsCoordinatedClasses = $db->getCoordinatedClassesByATeacher($ssn);
                $k = 1;
                    echo <<<_MODALCOORDINATOR
                    <table id="coordinatedClassesTable" class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-center"></th>
                                                    <th scope="col" class="text-center">COORDINATED CLASS</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyCoordinatedClasses">
_MODALCOORDINATOR;
                if($rowsCoordinatedClasses->num_rows){
                    foreach ($rowsCoordinatedClasses as $coordinatedClass) {
                        $class = $coordinatedClass['classID'];

                         echo '<tr id="tr_coordinate_'.$class.'">';
                                                            
                        echo <<<_COORDINATED_CLASSES
                                                                <td class="text-center"></td>
                                                                <td class="text-center">$class</td>
                                                                <td class="text-center"><button type="button" id="trashButtonCoordinatedClass_$k" class="btn btn-danger btn-lg" onclick='trashButtonCoordinatedClass(this,"$ssn","$class")'><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button> </td>
                                                            </tr>
_COORDINATED_CLASSES;
                        $k++;
                    }
                }
                $classes = $db->getNotCoordinatedClasses();
                if($classes->num_rows > 0){
                echo <<<_LASTROW
                                                <tr id="rowPlusCoordinatedClass">
                                                <td class="text-center"></td>
                                                <td class='text-center'><select id='selectedCoordinatedClass'>
_LASTROW;
                    foreach ($classes as $c) {
                        $c = $c['classID'];
                        echo "<option value=$c>$c</option>";
                    }
                    echo "</select> </td>";
                    echo <<<_ENDMODAL
                                                    </select></td>
                                                    
                                                    <td class="text-center"><button type="button" id="addCoordinatedClass" class="btn btn-success btn-lg" onclick='addCoordinatedClassFunction(this, "$ssn","$c")'><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button> </td>
                                                    </tr>
        
                                                    </tbody>
                                                    </table>
_ENDMODAL;
                }
                    echo <<<_MODAL
                    <table id="classSubjectTable" class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">CLASS</th>
                                                    <th scope="col">SUBJECT</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyClassSubject">
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
            if (isset($_POST["codFisc"]) && $_POST["codFisc"] != "" && isset($_POST["class"]) && $_POST["class"] != "" && isset($_POST["subject"]) && $_POST["subject"] != "") {

                $codFisc = $_POST["codFisc"];
                $classID = $_POST["class"];
                $subject = $_POST["subject"];
                
                if ($db->addSubjectTeachedInAClassByATeacher($codFisc, $classID, $subject))
                    echo 1;
                else echo 0;
                exit;
            }
        }
        elseif($_POST["event"] == "add") {
            //Delete a teacher
            if (isset($_POST["codFisc"]) && $_POST["codFisc"] != "" && isset($_POST["name"]) && $_POST["name"] != "" && isset($_POST["surname"]) && $_POST["surname"] != "" ) {
                $hashedPw = password_hash($_POST["codFisc"], PASSWORD_DEFAULT);
                if ($db->insertOfficialAccount("Teachers", $_POST["codFisc"], $hashedPw, $_POST["name"], $_POST["surname"], 0) )
                    echo 1;
                else echo 0;
                exit;
            }
            else echo 0;
            exit;
        }
        elseif($_POST["event"] == "addCoordinatedClass") {
            //Delete a teacher
            if (isset($_POST["codFisc"]) && $_POST["codFisc"] != "" && isset($_POST["class"]) && $_POST["class"] != "" ) {
                if ($db->insertCoordinatedClass($_POST["codFisc"], $_POST["class"] ))
                    echo 1;
                else echo 0;
                exit;
            }
            else echo 0;
            exit;
        }
        elseif($_POST["event"] == "deleteCoordinatedClass") {
            //Delete a teacher
            if (isset($_POST["codFisc"]) && $_POST["codFisc"] != "" && isset($_POST["classID"]) && $_POST["classID"] != "") {
                if ($db->deleteClassCoordinator($_POST["codFisc"], $_POST["classID"]))
                    echo 1;
                else echo 0;
                exit;
            }
            else echo 0;
            exit;
        }
    }
}
