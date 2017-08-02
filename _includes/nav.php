	<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">

					<!-- User menu -->
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="#" class="media-left"><img src="assets/images/placeholder.jpg" alt="" class="img-circle img-sm"></a>
								<div class="media-body">
									<span class="media-heading text-semibold">Car importer</span>
									<div class="text-size-mini text-muted">
										<i class="icon-arrow-right5 text-size-small"></i>by Holycow24.com
									</div>
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
										<li>
											<!--a href="#"><i class="icon-cog3"></i></a-->
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /user menu -->


					<!-- Main navigation -->
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">

								<!-- Main -->
								<li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
								<li <?php if ($_SERVER[ 'SCRIPT_NAME' ] == '/index.php') echo 'class="active"' ?>><a href="index.php"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
								<li <?php if ($_SERVER[ 'SCRIPT_NAME' ] == '/importer.php') echo 'class="active"' ?>><a href="importer.php"><i class="icon-home4"></i> <span>Importer</span></a></li>
								<li <?php if ($_SERVER[ 'SCRIPT_NAME' ] == '/qa.php') echo 'class="active"' ?>><a href="index.php"><i class="icon-home4"></i> <span>QA</span></a></li>
								<li <?php if ($_SERVER[ 'SCRIPT_NAME' ] == '/exporter.php') echo 'class="active"' ?>><a href="index.php"><i class="icon-home4"></i> <span>Exporter</span></a></li>
								<!--li>
									<a href="#"><i class="icon-stack2"></i> <span>Page layouts</span></a>
									<ul>
										<li><a href="/layout_navbar_fixed.html">Fixed navbar</a></li>
										<li><a href="/layout_navbar_sidebar_fixed.html">Fixed navbar &amp; sidebar</a></li>
										<li><a href="/layout_sidebar_fixed_native.html">Fixed sidebar native scroll</a></li>
										<li><a href="/layout_navbar_hideable.html">Hideable navbar</a></li>
										<li><a href="/layout_navbar_hideable_sidebar.html">Hideable &amp; fixed sidebar</a></li>
										<li><a href="/layout_footer_fixed.html">Fixed footer</a></li>
										<li class="navigation-divider"></li>
										<li><a href="/boxed_default.html">Boxed with default sidebar</a></li>
										<li><a href="/boxed_mini.html">Boxed with mini sidebar</a></li>
										<li><a href="/boxed_full.html">Boxed full width</a></li>
									</ul>
								</li-->
								
								<!-- /main -->


							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->