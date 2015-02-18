<style type="text/css">
	
	
    table.data {
        width: 85%;
    }
    table
	{
		border-collapse:collapse;
	}
	table.table th, td
	{
		border: 1px solid black;
	}
</style>
<div class="main">
	<div class= "box">
	
    <div id="formHeading" class="head"><h1><?php echo __("360 Feedback: ".$feedbackEmp->getFirstAndLastNames()) ?></h1></div>
	<div id="tableWrapper">
		 <div class="inner">
			<table class="table hover">
			<thead>
				<tr>
					<th></th>
		        	<th style="width:50%"><?php echo __("Strong Points") ?></th>
		        	<th style="width:50%"><?php echo __("Weak Points") ?></th>
		        </tr>
		     </thead>
		     <tbody>
				<?php if(count($multisourceFeedbackList) < 1 ) { ?>
					<tr>
						<td colspan="3">No results found.</td>
					</tr>
				<?php } else {
						$count = 1;
						foreach ($multisourceFeedbackList as $feedback) { ?>
		 			<tr>
		 				<td><?php echo $count;?></td>
						<td><?php echo nl2br($feedback->getPositiveFeedback());?></td>
					    <td><?php echo nl2br($feedback->getNegativeFeedback());?></td>
					</tr>
				<?php 	
					$count++;
						}
				}?>
			</tbody>
			</table>
		</div>
	</div>
</div>
</div>