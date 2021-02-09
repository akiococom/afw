<!-- Header -->
<header>
	<!-- Navbar -->
	<nav class="{{if $is_top}}js-navbar-scroll {{else}}navbar-bg-onscroll {{/if}} navbar fixed-top navbar-expand-lg navbar-dark">
		<div class="container-fluid">
			<a class="navbar-brand" href="{{$config.base}}">
				<h1><img src="{{$config.base}}assets/plant-image/h_fc.png" alt="{{$config.app_name}}" height="32"/></h1>
			</a>

			<button class="navbar-toggler" type="button"
							data-toggle="collapse"
							data-target="#navbarTogglerDemo"

							aria-controls="navbarTogglerDemo"
							aria-expanded="false"
							aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarTogglerDemo">
				<ul class="navbar-nav mt-2 mt-lg-0">
					<li class="nav-item mr-4 mb-2 mb-lg-0">
						{{*<a class="nav-link active" href="index.html">Back to UI Kit</a>*}}
					</li>
				</ul>
				<ul class="navbar-nav ml-auto mt-2 mt-lg-0">
					<li class="nav-item mr-4 mb-2 mb-lg-0">
						<a class="nav-link active" href="{{$config.base}}app/messages/">{{'お知らせ'|lang}}</a>
					</li>
					<li class="nav-item mr-4 mb-2 mb-lg-0">
						<a class="nav-link active" href="{{$config.base}}app/events/">{{'ライブ・イベント'|lang}}</a>
					</li>
					<li class="nav-item mr-4 mb-2 mb-lg-0">
						<a class="nav-link active" href="{{$config.base}}app/pages/">{{'コンテンツ'|lang}}</a>
					</li>
					{{if $session.user_id}}
					{{else}}
						<li class="nav-item mr-4 mb-2 mb-lg-0">
							<a class="nav-link active" href="{{$config.base}}app/signup/">{{'はじめての方へ'|lang}}</a>
						</li>
					{{/if}}
				</ul>
				<div>
					{{if $session.user_id}}
						<a class="btn btn-primary" href="{{$config.base}}user/{{$session.user_key}}/">
							<i class="fas fa-user-alt mr-1"></i>{{if $session.user_nickname}}{{$session.user_nickname}}{{'さん'|lang}}{{/if}}
							{{'マイページ'|lang}}
						</a>
					{{else}}
						<a class="btn btn-primary" href="{{$config.base}}app/signin/">
							<i class="fas fa-sign-in-alt mr-1"></i> {{'サインイン'|lang}}
						</a>
					{{/if}}
				</div>
			</div>
		</div>
	</nav>
	<!-- End Navbar -->
</header>
<!-- End Header -->
<main>
