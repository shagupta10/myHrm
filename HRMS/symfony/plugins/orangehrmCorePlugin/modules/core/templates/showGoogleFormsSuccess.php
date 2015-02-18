<div class="box">
	<div class="head">
		<h1 id="formHeading">
            <?php echo ('HR Policies and Forms'); ?>
        </h1>
	</div>
	<div class="inner">
		<table class="table" style="width: 100%;">
			<tr>
				<td style="vertical-align: text-top">
					<table class="table" style="width: 50%;">
						<thead>
							<tr>
								<th style="width: 100%;" class="center"><?php echo "Forms"; ?></th>
							</tr>
						</thead>
						<tbody>
							    <?php
											foreach ( $formsArray as $key => $val ) {
												?>
							    	<tr>
								<td><a href="#" onclick="openGoogleDocs('<?php echo $key;?>')" /><?php echo ($val);?></td>
							</tr>
							   <?php
											}
											?>
							</tbody>
					</table>
				</td>
				<td style="vertical-align: text-top">
					<table class="table" style="width: 50%;">
						<thead>
							<tr>
								<th style="width: 100%;" class="center"><?php echo "Policies"; ?></th>
							</tr>
						</thead>
						<tbody>
								<?php
								foreach ( $policiesArray as $key => $val ) {
									?>
							    	<tr>
								<td><a href="#" onclick="openGoogleDocs('<?php echo $key;?>')" /><?php echo $val;?></td>
							</tr>
							   <?php
								}
								?>
							</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>

<script>
					function openGoogleDocs(url) {
						var win = window.open(url,"_blank");
					}
					</script>
