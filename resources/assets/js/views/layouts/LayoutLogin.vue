<template>
	<div class="main-wrapper login-body">
		<div class="login-wrapper">
			<div class="container">
				<router-link to="/admin"><img src="/assets/img/logo.png" alt="Logo" class="img-fluid logo-dark mb-2"></router-link>
				<div class="loginbox">
					<div class="login-right">
						<div class="login-right-wrap">
							<h1 v-if="currentPath=='/login' || currentPath == '/'">{{ $t('layout_login.login') }}</h1>
							<h1 v-else-if="currentPath=='/register'">Register</h1>
							<h1 v-else-if="currentPath=='/forgot-password'">Forgot Password?</h1>
							<p class="account-subtitle" v-if="currentPath=='/login' || currentPath == '/'">{{ $t('layout_login.access_dashboard') }}</p>
							<p class="account-subtitle" v-if="currentPath=='/register'">{{ $t('layout_login.access_dashboard') }}</p>
							<p class="account-subtitle" v-if="currentPath=='/forgot-password'">Enter your email to get a password reset link</p>
							<router-view></router-view>
							<div class="text-center dont-have" v-if="currentPath=='/forgot-password'">Remember your password?  <router-link to="/login">Login</router-link></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/babel">
export default {
  computed: {
  currentPath() {
    return this.$route.path
  }
}
}
</script>
