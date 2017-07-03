<div class="row content">
	<div class="col-sm-4">
		<h2 id="title"><small>Rejected Vacations</small></h2>
	</div>
</div>
<hr>
<div class="date-panel" id="datepicker">
	<div class="panel panel-default">
		<div class="panel-heading">Chose dates and click submit</div>
		<div class="panel-body">
			<form name="date" id="date" onsubmit="submitcheck();">
				<div class="form-group" id='names'>
					<label for="names" class="control-label">User</label>
					<select name="name" id="name" class="form-control">
					<?php
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