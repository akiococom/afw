<header class=" sticky-top">
	<nav class="nav-main-header navbar navbar-expand-lg navbar-dark bg-dark">
		<a class="navbar-brand" href="{{$config.url}}">
			<img src="{{$config.base}}common/images/logo.png" class="img-logo d-inline-block align-middle"
				alt="{{$config.app_name}}">
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav-menu" aria-controls="nav-menu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="nav-menu">
			<ul class="navbar-nav mr-auto mt-2 mt-lg-0 ml-4">
				<li class="nav-item mx-2">
					<a class="nav-link" href="{{$config.base}}">{{''|lang}}</a>
				</li>
			</ul>

			{{if $session.user_id && $session.password_md5 != 'guest'}}
				<a href="{{$config.base}}user/{{$session.user_key}}/">
					<i class="fas fa-user fa-2x mr-2"></i>
				</a>
				<a href="{{$config.base}}user/{{$session.user_key}}/">
					{{$session.user_nickname}}
				</a>
			{{else}}
				<a href="#" class="a-modal-login">
					<i class="fas fa-sign-in fa-2x mr-2"></i>
				</a>
				<a href="#" class="a-modal-login">
					{{'ログイン/会員登録(無料)'|lang}}
				</a>
			{{/if}}
		</div>
	</nav>
</header>
<main class="{{if $full}}container-fluid pl-0 pr-0{{else}}container pt-5{{/if}}">
