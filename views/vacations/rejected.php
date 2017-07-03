<div class="row content">
	<div class="col-sm-4">
		<h2 id="title"><small>Rejected Vacations</small></h2>
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
		<button type="button" class="btn btn-basic" onclick="apply_filter();"><span class="glyphicon glyphicon-filter"></span> Filter</button>
	</div>
</div>
<hr>
<?php
echo "<script>pending_requests($pending);</script>";
if($vacations->data){
	echo "<table class='table table-striped'><tr class='danger'><th>Name</th><th>Requested on</th><th>From</th><th>Until</th><th>Total Requested Days</th><th>Remaining days</th></tr>";
	foreach($vacations->data as $vacation){
	echo "<tr><td>".$vacation['name']."</td><td>".$vacation['requested']."</td><td>".$vacation['start_date']."</td><td>".$vacation['end_date']."</td><td>".$vacation['total_days']."</td><td>".$vacation['remaining_days']."</td></td></tr>";
	}
	echo "</table></p>";

	//render pagination if there are more than 1 page
	if($pages>1){
		echo "<div class='pagem'><ul class='pagination pagination-sm'>";
		if($page>1){
			echo "<li><a href='#' onclick=\"load_page('vacations','rejected',$page-1,'$f');\">«</a></li>";
		}
		for($p=1; $p<=$pages; $p++){
			if($page and $page==$p){
				$class='active';
			}
			else{
				$class='';
			}
			echo "<li class='$class'><a href='#' data-id='$p' onclick=\"load_page('vacations','rejected',$p,'$f');\">$p</a></li>";
		}
		if($page!=$pages){
			echo "<li><a href='#' onclick=\"load_page('vacations','rejected',$page+1,'$f');\">»</a></li>";
		}
		echo "</ul></div>";
	}
}
else{
	echo "<div class='alert alert-danger'>There are no Rejected Vacations!</div>";
}