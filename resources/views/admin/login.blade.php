@php
    $config = DB::table('configs')->select('icon','logo')->first();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<title>{{ config('app.name') }}</title>
	<!--favicon-->
	<link rel="icon" href="/icons/icon.svg" type="image/png" />
	<!-- loader-->
	<link href="/admin/assets/css/pace.min.css" rel="stylesheet" />
	<script src="/admin/assets/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="/admin/assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&family=Roboto&display=swap" />
	<!-- Icons CSS -->
	<link rel="stylesheet" href="/admin/assets/css/icons.css" />
	<!-- App CSS -->
	<link rel="stylesheet" href="/admin/assets/css/app.css" />
</head>

<body class="bg-login">
	<!-- wrapper -->
	<div class="wrapper">
		<div class="section-authentication-login d-flex align-items-center justify-content-center mt-4">
			<div class="row">
				<div class="col-12 col-lg-8 mx-auto">
					<div class="card radius-15 overflow-hidden">
						<div class="row g-0">
							<div class="col-xl-6">
								<div class="card-body p-5">
									<div class="text-center">
										<img src="{{ Storage::url($config->icon) }}" width="80" alt="">
										<h3 class="mt-4 font-weight-bold">
											Bon retour.
										</h3>
									</div>
									<div class="">
										<div class="login-separater text-center mb-4"> <span> CONNEXION AVEC L'EMAIL</span>
											<hr>
										</div>
										<div class="form-body">
                                            @livewire('Connexion')
										</div>
									</div>
								</div>
							 </div>
							<div class="col-xl-6 bg-login-color d-flex align-items-center justify-content-center">
								<img src="https://blog.comexplorer.com/hubfs/strate%CC%81gie-crm-1.webp" class="img-fluid  d-none d-sm-block" alt="..." >
							</div>
						</div>
						<!--end row-->
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end wrapper -->
</body>

<!--plugins-->
<script src="/admin/assets/js/jquery.min.js"></script>
<!--Password show & hide js -->
<script>
	$(document).ready(function () {
		$("#show_hide_password a").on('click', function (event) {
			event.preventDefault();
			if ($('#show_hide_password input').attr("type") == "text") {
				$('#show_hide_password input').attr('type', 'password');
				$('#show_hide_password i').addClass("bx-hide");
				$('#show_hide_password i').removeClass("bx-show");
			} else if ($('#show_hide_password input').attr("type") == "password") {
				$('#show_hide_password input').attr('type', 'text');
				$('#show_hide_password i').removeClass("bx-hide");
				$('#show_hide_password i').addClass("bx-show");
			}
		});
	});
</script>

</html>