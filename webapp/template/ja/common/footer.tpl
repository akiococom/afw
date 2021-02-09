</main>
<!-- Footer -->
<footer class="bg-dark py-5">
	<div class="container">
		<div class="row">
			<div class="col-md-8 text-center text-md-left mb-3 mb-md-0">
				<small class="text-white">&copy; 2021 <a class="text-white" href="{{$config.base}}">PLANT</a></small>
			</div>

			<div class="col-md-4 align-self-center">
				<ul class="list-inline text-center text-md-right mb-0">
					<li class="list-inline-item mx-2" data-toggle="tooltip" data-placement="top" title="Facebook">
						<a class="text-white" target="_blank" href="https://www.facebook.com/">
							<i class="fab fa-facebook"></i>
						</a>
					</li>
					<li class="list-inline-item mx-2" data-toggle="tooltip" data-placement="top" title="Instagram">
						<a class="text-white" target="_blank" href="https://www.instagram.com/">
							<i class="fab fa-instagram"></i>
						</a>
					</li>
					<li class="list-inline-item mx-2" data-toggle="tooltip" data-placement="top" title="Twitter">
						<a class="text-white" target="_blank" href="https://twitter.com/">
							<i class="fab fa-twitter"></i>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</footer>
<!-- End Footer -->

<!-- JAVASCRIPTS (Load javascripts at bottom, this will reduce page load time) -->
<!-- Global Vendor -->
<script src="{{$config.base}}assets/vendors/jquery.min.js"></script>
<script src="{{$config.base}}assets/vendors/jquery.migrate.min.js"></script>
<script src="{{$config.base}}assets/vendors/popper.min.js"></script>
<script src="{{$config.base}}assets/vendors/bootstrap/js/bootstrap.min.js"></script>

<!-- Components Vendor	-->
<script src="{{$config.base}}assets/vendors/jquery.parallax.js"></script>
<script src="{{$config.base}}assets/vendors/typedjs/typed.min.js"></script>
<script src="{{$config.base}}assets/vendors/slick-carousel/slick.min.js"></script>
<script src="{{$config.base}}assets/vendors/counters/waypoint.min.js"></script>
<script src="{{$config.base}}assets/vendors/counters/counterup.min.js"></script>

<!-- Theme Settings and Calls -->
<script src="{{$config.base}}assets/js/global.js"></script>

<!-- Theme Components and Settings -->
<script src="{{$config.base}}assets/js/vendors/parallax.js"></script>
<script src="{{$config.base}}assets/js/vendors/carousel.js"></script>
<script src="{{$config.base}}assets/js/vendors/counters.js"></script>
<script src="{{$config.base}}assets/js/plant.js"></script>
<!-- END JAVASCRIPTS -->

{{include file="common/footer_script.tpl"}}