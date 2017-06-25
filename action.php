<?php

require_once('vacation.php');
$spvacation= new vacation;
$f='';
if(isset($_GET['date'])){
	$f=$_GET['date'];
	$filter=explode(",",$_GET['date']);
	$from=$filter[0];
	$to=$filter[1];
	$spvacation->initFilter($from,$to);
}
if(isset($_GET['content']) && $_GET['content']){
	switch ($_GET['content']) {
		case 'approved':
			?>
			<div class="row content">
				<div class="col-sm-4"><h2><small>Approved Vacations</small></h2>
				</div>
				<div class="col-sm-8 form-inline filter" id="datepicker">
					<label for="from" class="control-label">From</label>
					<div class="input-group" id='fromerror'>
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						<input type="text" id="from" name="from" class="form-control">
					</div>
					<label for="to"  class="control-label">To</label>
					<div class="input-group" id="toerror">
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						<input type="text" id="to" name="to" class="form-control">
					</div>

					<button type="button" class="btn btn-basic" onclick="applyFilter('approved');"><span class="glyphicon glyphicon-filter"></span> Filter</button>
				</div>
			</div>
			<hr> 
			<div id="confirm" class="modal fade" role="dialog">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 class="modal-title">Remove Vacation?</h4>
			        <input type="hidden" id="reid" name="reid" class="form-control">
			      </div>
			      <div class="modal-body">
			        <p>Are you sure you want to remove vacation for <span id="modalinfo" class="text-primary"></span></p>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-danger" id='btnyes' data-dismiss="modal" data-backdrop="false">yes</button>
			        <button type="button" class="btn btn-basic" data-dismiss="modal">No</button>
			      </div>
			    </div>
			  </div>
			</div>
			<?php
			if(isset($_GET['page'])){
				$page=$_GET['page'];
				$vacations=$spvacation->approvedVacations(($page-1)*10);
			}
			else{
				$vacations=$spvacation->approvedVacations();
			}

			if($vacations){
				echo "<table class='table table-striped'><tr class='success'><th>Name</th><th>Requested on</th><th>From</th><th>Until</th><th>Total Requested Days</th><th>Remaining days</th><th>Actions</th></tr>";
				foreach($vacations as $vacation){
					echo "<tr><td>".$vacation['name']."</td><td>".$vacation['requested']."</td><td>".$vacation['start_date']."</td><td>".$vacation['end_date']."</td><td>".$vacation['total_days']."</td><td>".$vacation['remaining_days']."</td><td><button type='button' id='removeButton' class='btn btn-default btn-sm'  data-toggle='modal' data-target='#confirm' data-placement='left' title='Remove Vacation' data-id='".$vacation['id']."' data-userid='".$vacation['user_id']."' data-info='".$vacation['name']."  from ".$vacation['start_date']." to " .$vacation['end_date']."'>Remove
	          <span class='glyphicon glyphicon-remove-circle'></span></button></td></tr>";
				}
				echo "</table></p>";
				if($spvacation->numApproved>10){
					if(!isset($page)){
						$page=1;
					}
					echo "<div class='pagem'><ul class='pagination pagination-sm'>";
					$pages=ceil($spvacation->numApproved/10);
					if($page>1){
						echo "<li><a href='#' onclick=\"load_page('approved',$page-1,'$f');\">«</a></li>";
					}
					for($p=1; $p<=$pages; $p++){
						if($page and $page==$p){
							$class='active';
						}
						else{
							$class='';
						}
						echo "<li class='$class'><a href='#' data-id='$p' onclick=\"load_page('approved',$p,'$f');\">$p</a></li>";
					}
					if($page!=$pages){
						echo "<li><a href='#' onclick=\"load_page('approved',$page+1,'$f');\">»</a></li>";
					}
					echo "</ul></div>";
				}
			}
			else{
				echo "<div class='alert alert-danger'>There are no Approved Vacations!</div>";
			}
			break;
		case 'rejected':
			?>
			<div class="row content">
				<div class="col-sm-4"><h2><small>Rejected Vacations</small></h2>
				</div>
				<div class="col-sm-8 form-inline filter" id="datepicker">
					<label for="from" class="control-label">From</label>
					<div class="input-group" id='fromerror'>
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						<input type="text" id="from" name="from" class="form-control">
					</div>
					<label for="to"  class="control-label">To</label>
					<div class="input-group" id="toerror">
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						<input type="text" id="to" name="to" class="form-control">
					</div>

					<button type="button" class="btn btn-basic" onclick="applyFilter('rejected');"><span class="glyphicon glyphicon-filter"></span> Filter</button>
				</div>
			</div>
			<hr>
			<?php
			if(isset($_GET['page'])){
				$page=$_GET['page'];
				$vacations=$spvacation->rejectedVacations(($page-1)*10);
			}
			else{
				$vacations=$spvacation->rejectedVacations();
			}		
			if($vacations){
				echo "<table class='table table-striped'><tr class='danger'><th>Name</th><th>Requested on</th><th>From</th><th>Until</th><th>Total Requested Days</th><th>Remaining days</th><th></th></tr>";
				foreach($vacations as $vacation){
					echo "<tr><td>".$vacation['name']."</td><td>".$vacation['requested']."</td><td>".$vacation['start_date']."</td><td>".$vacation['end_date']."</td><td>".$vacation['total_days']."</td><td>".$vacation['remaining_days']."</td><td></td></tr>";
				}
				echo "</table></p>";
				if($spvacation->numRejected>10){
					if(!isset($page)){
						$page=1;
					}
					echo "<div class='pagem'><ul class='pagination pagination-sm'>";
					$pages=ceil($spvacation->numRejected/10);
					if($page>1){
						echo "<li><a href='#' onclick=\"load_page('rejected',$page-1,'$f');\">«</a></li>";
					}
					for($p=1; $p<=$pages; $p++){
						if($page and $page==$p){
							$class='active';
						}
						else{
							$class='';
						}
						echo "<li class='$class'><a href='#' onclick=\"load_page('rejected',$p,'$f');\">$p</a></li>";
					}
					if($page!=$pages){
						echo "<li><a href='#' onclick=\"load_page('rejected',$page+1,'$f');\">»</a></li>";
					}
					echo "</ul></div>";
				}
			}
			else{
				echo "<div class='alert alert-danger'>There are no Rejected Vacations!</div>";
			}
			break;
		case 'request':
			?>		
			<h2><small>Request your well deserved vacation here</small></h2>
			<hr>
			<p>
			<div class="date-panel" id="datepicker">
			<div class="panel panel-default">
			  <div class="panel-heading">Chose dates and click submit</div>
			  <div class="panel-body">
				<form name="date" id="date" onsubmit="submitcheck();">
				<div class="form-group" id='names'>
					<label for="names" class="control-label">User</label>
					<select name="name" id="name" class="form-control">
					<?php
						$names=$spvacation->users();
						foreach($names as $name){
							echo "<option value='".$name['id']."'>".$name['name']."</option>";
						}
  					?>
  					</select>
				</div>
				<div class="form-group" id='fromerror'>
					<label for="from" class="control-label">From</label>
					<input type="text" id="from" name="from" class="form-control">
				</div>
				<div class="form-group" id="toerror">
					<label for="to"  class="control-label">To</label>
					<input type="text" id="to" name="to" class="form-control">
				</div>
				<br />
				<input type='submit' class="btn btn-success" value='Submit' style="float:right">
				</form>
			  </div>
			  </div>
			</div>
			
			<?php
			break;
		case 'approve':
			?>
			<div class="row content">
				<div class="col-sm-4"><h2><small>Approve Vacations</small></h2>
				</div>
				<div class="col-sm-8 form-inline filter" id="datepicker">
					<label for="from" class="control-label">From</label>
					<div class="input-group" id='fromerror'>
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						<input type="text" id="from" name="from" class="form-control">
					</div>
					<label for="to"  class="control-label">To</label>
					<div class="input-group" id="toerror">
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						<input type="text" id="to" name="to" class="form-control">
					</div>

					<button type="button" class="btn btn-basic" onclick="applyFilter('approve');" ><span class="glyphicon glyphicon-filter"></span> Filter</button>
				</div>
			</div>			
			<hr>
			<p>
			<?php
			if(isset($_GET['page'])){
				$page=$_GET['page'];
				$vacations=$spvacation->vacationRequests(($page-1)*10);
			}
			else{
				$page=1;
				$vacations=$spvacation->vacationRequests();
			}		
			if($vacations){
				echo "<table class='table table-striped'><th>Name</th><th>Requested on</th><th>From</th><th>Until</th><th>Total Requested Days</th><th>Remaining days</th><th>actions</th>";
				foreach($vacations as $vacation){
					echo "<tr><td>".$vacation['name']."</td><td>".$vacation['requested']."</td><td>".$vacation['start_date']."</td><td>".$vacation['end_date']."</td><td>".$vacation['total_days']."</td><td>".$vacation['remaining_days']."</td><td><button type='button' class='btn btn-success' onclick='approve_vacation(".$vacation['id'].",".$vacation['user_id'].",".$page.");'>Approve</button> <button type='button' class='btn btn-danger' onclick='reject_vacation(".$vacation['id'].");'>Reject</button></td></tr>";
				}
				echo "</table></p>";
				if($spvacation->numPending>10){
					if(!isset($page)){
						$page=1;
					}
					echo "<div class='pagem'><ul class='pagination pagination-sm'>";
					$pages=ceil($spvacation->numPending/10);
					if($page>1){
						echo "<li><a href='#' onclick=\"load_page('approve',$page-1,'$f');\">«</a></li>";
					}
					for($p=1; $p<=$pages; $p++){
						if($page and $page==$p){
							$class='active';
						}
						else{
							$class='';
						}
						echo "<li class='$class'><a href='#' onclick=\"load_page('approve',$p,'$f');\">$p</a></li>";
					}
					if($page!=$pages){
						echo "<li><a href='#' onclick=\"load_page('approve',$page+1,'$f');\">»</a></li>";
					}
					echo "</ul></div>";
				}
			}
			else{
				echo "<div class='alert alert-success'>Hooray! There are no pending vacations!</div>";
			}
			break;
		case 'pending':
			$spvacation->numVacations();
			echo $spvacation->numPending;
			break;
		case 'adduser':
			?>			
			<h2><small>Add new user</small></h2>
			<hr>
			<p>
			<div class="adduser" id="adduser">
			<div class="panel panel-default">
			  <div class="panel-heading">Enter details and Save</div>
			  <div class="panel-body">
				<form name="date" id="date" class="form-Horizontal" onsubmit="adduser();">
				<div class="form-group" id='nameerror'>
					<label class="col-sm-2 control-label">Name</label>
     				<div class="col-sm-4">
						<input type="text" id="name" name="name" class="form-control">
					</div>
				</div>
				<div class="form-group" id="nameerror">
					<label class="col-sm-2 control-label">Max Number of Vacation Days</label>
     				<div class="col-sm-4">
						<input type="text" id="days" name="days" class="form-control">
					</div>
				</div>
				<br />
				<input type='submit' class="btn btn-success" value='Save' style="float:right">
				</form>
			  </div>
			  </div>
			</div>
			
			<?php
			break;
	}
}

if(isset($_GET['action']) && $_GET['action']){
	switch ($_GET['action']) {
		case 'addnew':
			if(isset($_POST['to']) && isset($_POST['from']) && $_POST['user_id']){
				echo $spvacation->requestVacation((int)$_POST['user_id'],$_POST['from'],$_POST['to']);
				}
			break;
		case 'vacationApprove':
			if(isset($_POST['id']) && isset($_POST['user_id'])){			
				print_r($spvacation->approveVacation((int)$_POST['id'],(int)$_POST['user_id']));
			}
			break;
		case 'vacationReject':
			if(isset($_POST['id'])){
				echo $spvacation->rejectVacation((int)$_POST['id']);
			}
			break;
		case 'vacationRemove':
			if(isset($_POST['id']) && isset($_POST['user_id'])){
				echo $spvacation->cancelVacation((int)$_POST['id'],$_POST['user_id']);
			}
			break;
		case 'adduser':
			if(isset($_POST['name']) && isset($_POST['days'])){
				echo $spvacation->adduser($_POST['name'],(int)$_POST['days']);
			}
			break;
		case 'test':
			print_r($spvacation->user(1));
			break;
	}
}

?>