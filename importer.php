<?php

include '_includes/dblogin.php'; 
include '_includes/head.php'; 

?>

<body>

<?php include '_includes/topnav.php'; ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php include '_includes/nav.php'; ?>


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Content area -->
				<div class="content">

					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Importer<a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>

						<div class="panel-body">
							<p>Please click the button to start the import.</p>
							<p>
								<form action="importer.php"  method="post">
									<input type="hidden" name="run_importer" value="true" />
									<button class="btn btn-primary btn-xlg"><i class="icon-comment-discussion position-left"></i> Start IMPORT</button>
								</form>
							</p>
<?php
	if($_POST["run_importer"] == true)
	{
		include '_includes/importer_scipt.php'; 
	}
?>
						</div>
						
						
						
						
					</div>

					<!-- Footer -->
					<?php include '_includes/footer.php'; ?>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>
