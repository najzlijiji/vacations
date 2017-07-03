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
<div class="row content">
	<div class="col-sm-4">
		<h2 id="title"><small>Approved Vacations</small></h2>
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
		<button type="button" class="btn btn-basic" onclick="applyFilter();"><span class="glyphicon glyphicon-filter"></span> Filter</button>
	</div>
</div>
<hr>
<?php
echo "<script>pending_requests($pending);</script>";
if($vacations->data){
	echo "<table class='table table-striped'><tr class='success'><th>Name</th><th>Requested on</th><th>From</th><th>Until</th><th>Total Requested Days</th><th>Remaining days</th><th>Actions</th></tr>";
	foreach($vacations->data as $vacation){
		echo "<tr><td>".$vacation['name']."</td><td>".$vacation['requested']."</td><td>".$vacation['start_date']."</td><td>".$vacation['end_date']."</td><td>".$vacation['total_days']."</td><td>".$vacation['remaining_days']."</td><td><button type='button' id='removeButton' class='btn btn-default btn-sm'  data-toggle='modal' data-target='#confirm' data-placement='left' title='Remove Vacation' data-id='".$vacation['id']."' data-userid='".$vacation['user_id']."' data-info='".$vacation['name']."  from ".$vacation['start_date']." to " .$vacation['end_date']."'>Remove <span class='glyphicon glyphicon-remove-circle'></span></button></td></tr>";
	}
	echo "</table></p>";

	//render pagination if there are more than 1 page
	if($pages>1){
		echo "<div class='pagem'><ul class='pagination pagination-sm'>";
		if($page>1){
			echo "<li><a href='#' onclick=\"load_page('vacations','approved',$page-1,'$f');\">«</a></li>";
		}
		for($p=1; $p<=$pages; $p++){
			if($page and $page==$p){
				$class='active';
			}
			else{
				$class='';
			}
			echo "<li class='$class'><a href='#' data-id='$p' onclick=\"load_page('vacations','approved',$p,'$f');\">$p</a></li>";
		}
		if($page!=$pages){
			echo "<li><a href='#' onclick=\"load_page('vacations','approved',$page+1,'$f');\">»</a></li>";
		}
		echo "</ul></div>";
	}
}
else{
	echo "<div class='alert alert-danger'>There are no Approved Vacations!</div>";
}