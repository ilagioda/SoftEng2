<?php
	require_once("basicChecks.php");
	
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
    require_once "loggedTeacherNavbar.php";
}	
	require_once("classTeacher.php");
	require_once("db.php");
	require_once("functions.php");
	
	$days = getCurrentSemester();

	if($days) {
		$now = new DateTime('now');
		$beginSemester = $days[0];
		$endSemester = $days[1];
	} // else --> summer holidays 


	$teacher=new Teacher();
	$db = new dbTeacher();
	$class = $_SESSION["comboClass"];
    
	if(isset($_POST['yesDelete'])) {
		if(!isset($_POST["lessontime"]) || !isset($_POST["comboHour"]) 
			|| !isset($_POST["comboClass"])) {
?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong><span class="glyphicon glyphicon-send"></span> Oh no! Something went wrong...</strong>
			</div>
<?php
		} else {
			$db->deleteDailyLesson($_POST['lessontime'], $_POST['comboHour'], $_POST['comboClass']);
?>
			<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong><span class="glyphicon glyphicon-send"></span> Daily lesson successfully deleted!</strong>
			</div>
<?php	
		} 

	}
		
	if(isset($_POST['editButton'])) {
		if(!isset($_POST["comboClass"]) || !isset($_POST["comboSubject"]) ||
			!isset($_POST["lessontime"]) || !isset($_POST["comboHour"]) || !isset($_POST["topics"]) 
				|| empty($_POST["topics"])){
?>
			<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong><span class="glyphicon glyphicon-send"></span> Oh no! Something went wrong...</strong>
			</div>
<?php
		} else {		
			$db->updateDailyLesson($_POST['lessontime'], $_POST['comboHour'], $_POST['comboClass'], $_POST['comboSubject'], $_POST['topics']);
?>
			<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong><span class="glyphicon glyphicon-send"></span> Daily lesson successfully updated!</strong>
			</div>
<?php	
		} 
	}
?>

<script>
$(document).ready(function(){	

	$('a[data-toggle="tab"]').click(function(e) {
		var target = $(e.target).attr("href"); // activated tab
	});
	
	$('[id^= "filteredTable-"]').hide();
	$('[id^= "titleSelectDate-"]').hide();
	$('[id^= "divPreviousNext-"]').hide();

	$('[id^= "buttonCalendar-"]').click(function(){
		$(this).hide();
		var subject = $(this).attr("data-subject");
		$('#lecturesTitle-'+subject).hide();
		$('#boxInfoToday-'+subject).hide();
		$('#filteredTable-'+subject).show();
		$('#btnShowAll-'+subject).prop("type", "button");
		$('#lecturesTable-'+subject).hide();
		$('#titleSelectDate-'+subject).show();
		$("#inputCalendar-"+subject).prop("type", "date");
		$("#divPreviousNext-"+subject).show();

	});
	
	$('[id^= "btnShowAll-"]').click(function(){
		var subject = $(this).attr("data-subject");
		$('#lecturesTitle-'+subject).show();
		$('#boxInfoToday-'+subject).show();
		$('#buttonCalendar-'+subject).show();
		$('#btnShowAll-'+subject).prop("type", "hidden");
		$("#inputCalendar-"+subject).prop("type", "hidden");
		$('#filteredTable-'+subject).hide();
		$('#lecturesTable-'+subject).show();
		$('#titleSelectDate-'+subject).hide();
		$("#divPreviousNext-"+subject).hide();

	});
	
	$('[id^= "inputCalendar-"]').change(function() {
		var subject = $(this).attr("data-subject");
		var date = $(this).val();
		
		callAjaxLoadLectures(date, subject);
	});

	$(".previous").click(function() {
		
		var subject = $(this).attr("data-subject");
		
		var actualDay = $('#inputCalendar-'+subject).val();
		actualDay = new Date(actualDay);
		
		var previousDay = actualDay.setDate(actualDay.getDate() - 1);
		previousDay = new Date(previousDay).toISOString().split('T')[0];

		callAjaxLoadLectures(previousDay, subject);
	});
	
	$(".next").click(function() {
		
		var subject = $(this).attr("data-subject");
		
		var actualDay = $('#inputCalendar-'+subject).val();
		actualDay = new Date(actualDay);
		
		var nextDay = actualDay.setDate(actualDay.getDate() + 1);
		nextDay = new Date(nextDay).toISOString().split('T')[0];

		callAjaxLoadLectures(nextDay, subject);
	});
});

// global
var curr = new Date;

var firstday = new Date(curr.setDate(curr.getDate() - curr.getDay()-6));
var lastday = new Date(curr.setDate(curr.getDate() - curr.getDay()+7));

firstday = firstday.toISOString().split('T')[0];  
lastday = lastday.toISOString().split('T')[0]; 

function callAjaxLoadLectures(date, subject) {
	
	$('#inputCalendar-'+subject).val(date); // set the date in the input field 

	var flag = 1; // flag to incate if the assignment is editable/erasable: 0 if it is editable/erasable, 1 otherwise
	if(date >= firstday && date <= lastday) {
		flag = 0;
	}
		
	$.ajax({
		type:		"POST",
		dataType:	"json",
		url:		"loadLectures.php",
		data:		"date="+date+"&subject="+subject,
		cache:		false,
		success:	function(response){ // RESPONSE = hour,topic
						$('#filteredTable-'+subject).empty();
						if(response.length > 0) {
							$('#filteredTable-'+subject).append(updateTableLectures(response, flag, subject, date));
						} else {
							$('#filteredTable-'+subject).append("<div class='alert alert-warning'><h4><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;No lectures for the selected date </h4></div>");
						}
					},
		error: 		function(){
						alert("Error: lectures not loaded");
					}
	});
}


function updateTableLectures(response, flag, subject, date) {

	var output = "<thead><tr class='active'><th class='text-center col-xs-6 col-sm-3'>Date</th><th class='text-center col-xs-6 col-sm-3'>Hour</th><th class='col-xs-6 col-sm-3'>Topics</th><th class='text-center col-xs-6 col-sm-3'></th></tr></thead><tbody>";
	
	for(var i=0; i<Object.keys(response).length; i++) { // foreach daily lesson topics
	
		var topics = "";
		res = response[i].split(",");
		var hour = res[0];
		
		for(var j=1; j<(res.length); j++) {
			topics += res[j]+",";
		}
		topics = topics.substr(0,topics.length-1);
		
		output += "<tr class='text-center'><td>"+date+"</td><td>"+hour+"</td><td>"+
					"<textarea readonly='readonly' style='border:none; background: none; outline: none;' rows='2'>"+
					topics+"</textarea></td><td>";
			
		if(flag === 0) {
			// lecture editable/erasable
			output += "<button type='button' class='btn btn-default btn-xs' style='width:20%'";
			output += "data-toggle='modal' data-target='#modalEdit'";
			output += "data-subject='"+subject+"' data-date='"+date+"' data-hour='"+hour+"' data-topics='"+topics+"' onclick='modalEdit(this)'>Edit</button>&emsp;";
			output += "<button type='button' class='btn btn-danger btn-xs' style='width:20%'";
			output += "data-toggle='modal' data-target='#modalDelete'";
			output += "data-subject='"+subject+"' data-date='"+date+"' data-hour='"+hour+"' data-topics='"+topics+"' onclick='modalDelete(this)'>Delete</button>";
		} else {
			output += "<span class='glyphicon glyphicon-ban-circle' aria-hidden='true' title='Not editable/erasable as it relates to past weeks...'></span>";
		}

		output += "</td></tr>";
	}

	output += "</tbody>";
	
	return output;
}

function modalDelete(obj) {
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectDelete").value = subject;
	
	var date = obj.getAttribute("data-date");
	document.getElementById("modalDateDelete").value = date;
	
	var hour = obj.getAttribute("data-hour");
	document.getElementById("modalHourDelete").value = hour;
	
	var topic = obj.getAttribute("data-topics");
	document.getElementById("modalTopicDelete").value = topic;
	
}

function modalEdit(obj) {
	
	var subject = obj.getAttribute("data-subject");
	document.getElementById("modalSubjectEdit").value = subject;
	
	var date = obj.getAttribute("data-date");
	document.getElementById("modalDateEdit").value = date;
	
	var hour = obj.getAttribute("data-hour");
	document.getElementById("modalHourEdit").value = hour;
	
	var topic = obj.getAttribute("data-topics");
	document.getElementById("modalTopicEdit").value = topic;
	
}
</script>


<ul class="nav nav-tabs">
  <li role="presentation"><a href="recordLesson.php">New record</a></li>
  <li role="presentation" class="active"><a href="#">View all records</a></li>
</ul>


<div class="panel panel-default" id="container">
	<div class="panel-body">
<?php 
	$subjects = $db->getSubjectsByTeacherAndClass2($_SESSION["user"], $class);
	if(count($subjects) > 0) { 
		navSubjects($subjects);							

		echo "<div id='myTabContent' class='tab-content'>";
		foreach($subjects as $subject) {
			if($subject == $subjects[0]) {
				echo "<div class='tab-pane fade active in' id='$subject'>";
			} else {
				echo "<div class='tab-pane fade' id='$subject'>";
			}
		$lectures = $db->getLecturesByTeacherClassAndSubject($_SESSION["user"], $class, $subject, $beginSemester, $endSemester);
		if(!empty($lectures)) {
?>
	<h2 id="lecturesTitle-<?php echo $subject; ?>">All lectures 
		<button class="btn btn-default pull-right" id="buttonCalendar-<?php echo $subject; ?>" data-subject="<?php echo $subject; ?>"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Select a date</button>
	</h2>
	<p class='text-center' id="titleSelectDate-<?php echo $subject; ?>"><strong>Select a date</strong></p>

	<input class="form-control" name="inputCalendar" id="inputCalendar-<?php echo $subject; ?>" data-subject="<?php echo $subject; ?>"
				type="hidden" min="<?php echo $beginSemester; ?>" max="<?php echo $endSemester ?>">
	
	<div id="divPreviousNext-<?php echo $subject; ?>">
		<ul class='pager'>
			<li class='previous' data-subject="<?php echo $subject; ?>"><a href='#'><span aria-hidden='true'>&larr;</span> Older</a></li>
			<li class='next' data-subject="<?php echo $subject; ?>"><a href='#'>Newer <span aria-hidden='true'>&rarr;</span></a></li>
		</ul>
	</div>	
	
	<table id="filteredTable-<?php echo $subject; ?>" class="table table-hover"></table>
	<table id="lecturesTable-<?php echo $subject; ?>" class="table table-hover text-center" style="border-collapse:collapse;">
		<thead><tr class="active">
			<th class='text-center col-xs-6 col-sm-3'>Date</th>
			<th class='text-center col-xs-6 col-sm-3'>Hour</th>
			<th class='text-center col-xs-6 col-sm-3'>Topics</th>
			<th class='text-center col-xs-6 col-sm-3'></th>
		</tr></thead>
		<tbody>
<?php
		foreach((array)$lectures as $value) {
				
			$args = explode(",",$value);
			$date = $args[0];
			$hour = $args[1];	
			$topics = "";
			for($j=2; $j<(count($args)); $j++) {
				// the text of the topic can contain the character ,
				$topics .= $args[$j].",";
			}
			$topics = substr($topics, 0, -1); // remove the last ,
			
?>	
		<tr>
			<td><?php echo $date;?></td>
			<td><?php echo $hour;?></td>
			<td><textarea readonly='readonly' style='border:none; background: none; outline: none;' rows='2'><?php echo $topics;?></textarea></td>						
			<td>
			<?php
				if($date >= date("Y-m-d", strtotime('monday this week')) && $date <= date("Y-m-d", strtotime('sunday this week'))) { 
			?>
				<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modalEdit" style="width:20%"
					<?php echo "data-subject='$subject' data-date='$date' data-hour='$hour' data-topics='$topics'"; ?>
					onclick="modalEdit(this)"> 
					Edit
				</button>
							
				<button type="button" class="btn btn-danger btn-xs"	data-toggle="modal" data-target="#modalDelete" style="width:20%"
					<?php echo "data-subject='$subject' data-date='$date' data-hour='$hour' data-topics='$topics'"; ?>	
					onclick="modalDelete(this)">
					Delete
				</button>
			<?php
				} else {
					echo "<span class='glyphicon glyphicon-ban-circle' aria-hidden='true' title='Not editable/erasable as it relates to past weeks...'></span>";
				}
			?>
			</td>
		</tr>
		<?php
		}
		?>
		</tbody>
	</table>
	<div class="text-center"><input type="hidden" class="btn btn-primary" value="Show all lectures" id="btnShowAll-<?php echo $subject; ?>" data-subject="<?php echo $subject; ?>"></div>
	<?php
			} else {
				echo "<div class='alert alert-warning'>
						<h4 id='assignmentsTitle'>
						<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&emsp;
						No lectures for this subject </h4></div>";
			}
			echo "</div>";
		}
		echo "</div>";
	}
	?>
					
</div>
</div>
	
	<!-- Modal -->
<div id="modalDelete" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body form-group text-center"></br>
	  
	    <button type="button" class="close" data-dismiss="modal">&times;</button>
		<form method="POST" action="">
			<table class="table table-hover text-center">
				<tr><td><label> Class </label></td>
					<td><input type="text" name="comboClass" value="<?php echo $class; ?>" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Subject </label></td>
					<td><input id="modalSubjectDelete" type="text" name="comboSubject" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input id="modalDateDelete" type="text" name="lessontime" readonly="readonly" style="border:none; background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Hour </label></td>
					<td><input id="modalHourDelete" type="text" name="comboHour" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Topics </label></td>
					<td>
						<textarea id="modalTopicDelete" name="topics" readonly="readonly" style="border:none;  background:none; outline: none;"></textarea>
					</td>
				</tr>
			</table>
	
			<h3><strong>Do you really want to delete this record?</strong> </h3>
			<button name="yesDelete" type="submit" class="btn btn-default btn-lg" onclick="this.form.action='viewAllLessonTopics.php'">Yes</button>
			<button type="submit" class="btn btn-default btn-lg" data-dismiss="modal">No</button>
		</form>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="modalEdit" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body form-group text-center"></br>
	  
	    <button type="button" class="close" data-dismiss="modal">&times;</button>
		<form method="POST" action="">
			<table class="table table-hover text-center">
				<tr><td><label> Class </label></td>
					<td><input type="text" name="comboClass" value="<?php echo $class; ?>" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Subject </label></td>
					<td><input id="modalSubjectEdit" type="text" name="comboSubject" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Date </label></td>
					<td><input id="modalDateEdit" type="text" name="lessontime" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Hour </label></td>
					<td><input id="modalHourEdit" type="text" name="comboHour" readonly="readonly" style="border:none;  background:none; outline: none;"></td>
				</tr>
				<tr>
					<td><label> Topics </label></td>
					<td>
						<textarea id="modalTopicEdit" class="form-control" name="topics" rows="4" style="width:60%"></textarea>
					</td>
				</tr>
			</table>
			<button name="editButton" type="submit" class="btn btn-success" style="width:20%" onclick="this.form.action='viewAllLessonTopics.php'">Edit</button>
			<button type="submit" class="btn btn-default" style="width:20%" data-dismiss="modal">Cancel</button>
		</form>
      </div>
    </div>

  </div>
</div>
	
	
	</div>
</div>

<?php 
    require_once("defaultFooter.php")
?>

