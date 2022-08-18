<template>

<!-- Header -->
			<div class="header">
			
				<!-- Logo -->
				<div class="header-left">
					<a href="index" class="logo">
						<img src="/assets/img/logo.png" alt="Logo">
					</a>
					<a href="/index" class="logo logo-small">
						<img src="/assets/img/logo-small.png" alt="Logo" width="30" height="30">
					</a>
				</div>
				<!-- /Logo -->
				<!-- Sidebar Toggle -->
				<a href="javascript:void(0);" id="toggle_btn">
					<i class="fas fa-bars"></i>
				</a>
				<!-- /Sidebar Toggle -->
				
				
				
				<div class="top-nav-search">
					<global-search class="hidden float-left md:block" /> 
				</div>
				
				
				<!-- Mobile Menu Toggle -->
				<a class="mobile_btn" id="mobile_btn">
					<i class="fas fa-bars"></i>
				</a>
				<!-- /Mobile Menu Toggle -->
				
				<!-- Header Menu -->
				<ul class="nav user-menu">
					<!-- User Menu -->
					<li class="nav-item dropdown has-arrow main-drop">
						<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
							<span class="user-img">
								<img :src="currentUser.avatar ? currentUser.avatar : '/assets/img/default-avatar.jpg'" id="previewImg" @error="altImg" alt="">
								<span class="status online"></span>
							</span>
							<span>{{currentUser.name}}</span>
						</a>
						<div class="dropdown-menu">
							<!-- <a class="dropdown-item" href="/profile.html"> <user-icon size="1.5x" class="custom-class"></user-icon> Profile</a> -->
							<router-link class="dropdown-item" to="/admin/settings"> <settings-icon size="1.5x" class="custom-class"></settings-icon>&nbsp;&nbsp;Settings</router-link>
							<a class="dropdown-item" @click="logout"> <log-out-icon size="1.5x" class="custom-class"> </log-out-icon>&nbsp;&nbsp;Logout</a>
						</div>
					</li>
					<!-- /User Menu -->
					
				</ul>
				<!-- /Header Menu -->
				
			</div>
			<!-- /Header -->

</template>

<script type="text/babel">
import { mapGetters, mapActions } from 'vuex'
import { HomeIcon } from 'vue-feather-icons'
import { UsersIcon } from 'vue-feather-icons'
import { FileTextIcon } from 'vue-feather-icons'
import { ClipboardIcon } from 'vue-feather-icons'
import { CreditCardIcon } from 'vue-feather-icons'
import { PackageIcon } from 'vue-feather-icons'
import { SettingsIcon } from 'vue-feather-icons'
import { UserIcon } from 'vue-feather-icons'
import { LogOutIcon } from 'vue-feather-icons'

export default {
  components: {
    HomeIcon,
    UsersIcon,
    FileTextIcon,
    ClipboardIcon,
    CreditCardIcon,
    PackageIcon,
	SettingsIcon,
	UserIcon,
	LogOutIcon
  },
  	mounted() {
		 $(document).ready(function(){
			$('.txtShowDiv').focus(function(){
				$('.sampleDiv').show();
			}).focusout(function(){
				$('.sampleDiv').hide();
			});
		});
       
        $('body').append('<div class="sidebar-overlay"></div>');
        $(document).on('click', '#mobile_btn', function() {
            $('.main-wrapper').toggleClass('slide-nav');
            $('.sidebar-overlay').toggleClass('opened');
            $('html').addClass('menu-opened');
            $('#task_window').removeClass('opened');
            return false;
        });
       

        $(document).on('click', '.top-nav-search .responsive-search', function() {
            $('.top-nav-search').toggleClass('active');
        });
        $('.sidebar-menu ul li:not(.submenu) a').click(function () {
            $("html").removeClass('menu-opened');
            $(".sidebar-overlay").removeClass('opened');
        });
	},
	computed: {
		...mapGetters('user', ['currentUser']),
		...mapGetters(['isSidebarOpen']),
	},
	methods: {
		...mapActions('user', ['fetchCurrentUser']),
		...mapActions('auth', ['logout']),
		...mapActions('modal', ['openModal']),
		...mapActions(['toggleSidebar']),
		altImg(e) {
			e.target.src = '/assets/img/default-avatar.jpg'
    	},
	},
}
</script>
<style lang="scss">
.hamburger {
  transition-property: opacity, filter;
  transition-duration: 0.15s;
}
.hamburger-inner {
  top: 50%;
  left: 4.5px;
  right: 4.5px;
}
.hamburger-inner,
.hamburger-inner::before,
.hamburger-inner::after {
  height: 2px;
  background-color: black;
  border-radius: 2px;
  position: absolute;
  transition-property: transform;
  transition-duration: 0.15s;
  transition-timing-function: ease;
}

.hamburger-inner::before,
.hamburger-inner::after {
  content: '';
  display: block;
  width: 100%;
}

.hamburger-inner::before {
  top: -5px;
}

.hamburger-inner::after {
  bottom: -5px;
}

.hamburger--arrowturn.is-active .hamburger-inner {
  transform: rotate(-180deg);
}

.hamburger--arrowturn.is-active .hamburger-inner::before {
  transform: translate3d(5px, 3px, 0) rotate(45deg) scale(0.5, 1);
}

.hamburger--arrowturn.is-active .hamburger-inner::after {
  transform: translate3d(5px, -3px, 0) rotate(-45deg) scale(0.5, 1);
}
</style>
