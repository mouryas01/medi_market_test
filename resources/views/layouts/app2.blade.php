<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Meta -->
		<meta name="description" content="Responsive Bootstrap4 Dashboard Template">
		<meta name="author" content="ParkerThemes">
		<link rel="shortcut icon" href="{{ asset('assets/img/fav.png') }}" />
		<!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<!-- Title -->
		<title>{{config('app.name', "Medimarket")}}</title>

		<!-- ************* Common Css Files ************* -->
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
		<!-- Bootstrap 3.3.6 -->
    	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
		<!-- Icomoon Font Icons css -->
		<link rel="stylesheet" href="{{ asset('assets/fonts/style.css') }}">
		<!-- ************* Vendor Css Files ************** -->
		<!-- DateRange css -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/daterange/daterange.css') }}" />			

		<!-- Data Tables -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/datatables/dataTables.bs4.css') }}" />
		<link rel="stylesheet" href="{{ asset('assets/vendor/datatables/dataTables.bs4-custom.css') }}" />
		<link href="{{ asset('assets/vendor/datatables/buttons.bs.css') }}" rel="stylesheet" />

		@stack('before-styles')	

		<!-- Main css -->
		<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
						
		@stack('after-styles')
		<script>
		function showloader(){
			$('#loading-wrapper').show();   
		}
		</script>
	</head>
	<body>

		<!-- Center Loader -->
		<div class="loading" style="display:none;">Loading&#8230;</div>

		<!-- Loading starts -->
		<div id="loading-wrapper">
			<div class="spinner-border" role="status">
				<span class="sr-only">Loading...</span>
			</div>
		</div>
		<!-- Loading ends -->


		<!-- Page wrapper start -->
		<div class="page-wrapper">		

			@if(!empty(Session::get('user_type')))

				@include('layouts.sidebar')							
				
				<!-- Page content start  -->
				<div class="page-content">
					
					@include('layouts.header')	
					
					@yield('content')								
				</div>
				<!-- Page content end -->

			@endif

		</div>
		<!-- Page wrapper end -->

		<!--*********** Required JavaScript Files ************-->
		<!-- Required jQuery first, then Bootstrap Bundle JS -->
		<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
		<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
		<script src="{{ asset('assets/js/moment.js') }}"></script>


		<!-- ************* Vendor Js Files ************* -->
		<!-- Slimscroll JS -->
		<script src="{{ asset('assets/vendor/slimscroll/slimscroll.min.js') }}"></script>
		<script src="{{ asset('assets/vendor/slimscroll/custom-scrollbar.js') }}"></script>

		<!-- Daterange -->
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="{{ asset('assets/vendor/daterange/daterange.js') }}"></script>
		<script src="{{ asset('assets/vendor/daterange/custom-daterange.js') }}"></script>

		<!-- Polyfill JS -->
		<script src="{{ asset('assets/vendor/polyfill/polyfill.min.js') }}"></script>

		<!-- Apex Charts -->
		<script src="{{ asset('assets/vendor/apex/apexcharts.min.js') }}"></script>
		<script src="{{ asset('assets/vendor/apex/admin/visitors.js') }}"></script>
		<script src="{{ asset('assets/vendor/apex/admin/deals.js') }}"></script>
		<script src="{{ asset('assets/vendor/apex/admin/income.js') }}"></script>
		<script src="{{ asset('assets/vendor/apex/admin/customers.js') }}"></script>

		<!-- Data Tables -->
		<script src="{{ asset('assets/vendor/datatables/dataTables.min.js') }}"></script>
		<script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
		
		<!-- Custom Data tables -->
		<script src="{{ asset('assets/vendor/datatables/custom/custom-datatables.js') }}"></script>
		<script src="{{ asset('assets/vendor/datatables/custom/fixedHeader.js') }}"></script>

		<!-- Download / CSV / Exdel / PDF / Copy / Print -->
		<script src="{{ asset('assets/vendor/datatables/buttons.min.js') }}"></script>
		<script src="{{ asset('assets/vendor/datatables/jszip.min.js') }}"></script>
		<script src="{{ asset('assets/vendor/datatables/pdfmake.min.js') }}"></script>
		<script src="{{ asset('assets/vendor/datatables/vfs_fonts.js') }}"></script>
		<script src="{{ asset('assets/vendor/datatables/html5.min.js') }}"></script>
		<script src="{{ asset('assets/vendor/datatables/buttons.print.min.js') }}"></script>

		@stack('before-scripts')
		<!-- Main JS -->
		<script src="{{ asset('assets/js/main.js') }}"></script>	
				   	
		@stack('after-scripts')
	</body>

</html>