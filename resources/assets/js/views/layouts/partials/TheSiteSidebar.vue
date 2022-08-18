<template>
  <!-- Sidebar -->
			<div class="sidebar" id="sidebar">
				<div class="sidebar-inner slimscroll">
        <vue-custom-scrollbar class="scroll-area"  :settings="settings" @ps-scroll-y="scrollHanle">
					<div id="sidebar-menu" class="sidebar-menu">
						<ul>

							<li class="menu-title"><span>Main</span></li>
							<li :class="{'active':currentPath === '/admin/dashboard'}">
								<router-link to="/admin/dashboard"><home-icon size="1.5x" class="custom-class"></home-icon> <span>{{$t('navigation.dashboard')}}</span></router-link>
							</li>
							<li :class="{'active':currentPath === '/admin/customers'|| currentPath === '/admin/customers/create' || currentPathName === 'customers.create' || currentPathName === 'customers.edit' || currentPathName === 'customers.view'}">
								<router-link to="/admin/customers"><users-icon size="1.5x" class="custom-class"></users-icon> <span>{{$t('navigation.customers')}}</span></router-link>
							</li>
              <li :class="{'active':currentPath === '/admin/items' || currentPathName === 'items.create' || currentPathName === 'items.edit' || currentPathName === 'items.view'}">
								<router-link to="/admin/items"><star-icon size="1.5x" class="custom-class"></star-icon> <span>{{$t('navigation.items')}}</span></router-link>
							</li>
							<li :class="{'active':currentPath === '/admin/estimates'|| currentPath === '/admin/estimates/create' || currentPathName === 'estimates.edit' || currentPathName === 'estimates.view'}">
								<router-link to="/admin/estimates"><file-text-icon size="1.5x" class="custom-class"></file-text-icon> <span>{{$t('navigation.estimates')}}</span></router-link>
							</li>
							<li :class="{'active':currentPath === '/admin/invoices'|| currentPath === '/admin/invoices/create' || currentPathName === 'invoices.edit' || currentPathName === 'invoices.view'}">
								<router-link to="/admin/invoices"><clipboard-icon size="1.5x" class="custom-class"></clipboard-icon> <span>{{$t('navigation.invoices')}}</span></router-link>
							</li>
							<li :class="{'active':currentPath === '/admin/payments'|| currentPath === '/admin/payments/create' || currentPathName === 'payments.edit' || currentPathName === 'payments.view'}">
								<router-link to="/admin/payments"><credit-card-icon size="1.5x" class="custom-class"></credit-card-icon> <span>{{$t('navigation.payments')}}</span></router-link>
							</li>
							<li :class="{'active':currentPath === '/admin/expenses'|| currentPath === '/admin/expenses/create' || currentPathName === 'expenses.edit'}">
								<router-link to="/admin/expenses"><package-icon size="1.5x" class="custom-class"></package-icon> <span>{{$t('navigation.expenses')}}</span></router-link
                >
							</li>
              <li class="submenu">
								<a href="#">   <bar-chart-icon size="1.5x" class="custom-class"></bar-chart-icon>
                <span> Reports</span> <span class="menu-arrow"></span></a> 
								<ul>
									<li><router-link to="/admin/reports/sales">Sales</router-link></li>
									<li><router-link to="/admin/reports/profit-loss">Profit & Loss</router-link></li>
									<li><router-link to="/admin/reports/expenses">Expenses</router-link></li>
                  <li><router-link to="/admin/reports/taxes">Taxes</router-link></li>
								</ul>
							</li>
							<li :class="{'active':currentPath === '/admin/settings/user-profile' || currentPath === '/admin/settings/company-info' || currentPath === '/admin/settings/preferences' || currentPath === '/admin/settings/notifications' || currentPath === '/admin/settings/customization' || currentPath === '/admin/settings/tax-types' ||  currentPath === '/admin/settings/payment-mode' ||  currentPath === '/admin/settings/custom-fields' ||  currentPath === '/admin/settings/notes' ||  currentPath === '/admin/settings/expense-category' ||  currentPath === '/admin/settings/mail-configuration' ||  currentPath === '/admin/settings/file-disk' ||  currentPath === '/admin/settings/backup'}">
								<router-link to="/admin/settings"><settings-icon size="1.5x" class="custom-class"></settings-icon> <span>{{$t('navigation.settings')}}</span></router-link>
							</li>
							<li  v-if="currentUser.role === 'super admin'" class="menu-title"> 
								<span>Pages</span>
							</li>
							<li v-if="currentUser.role === 'super admin'" :class="{'active':currentPath === '/admin/users'|| currentPath === '/admin/users/create' || currentPathName === 'users.edit'}"> 
								<router-link to="/admin/users"><user-icon size="1.5x" class="custom-class"></user-icon> <span>{{$t('navigation.users')}}</span></router-link>
							</li>
						</ul>
					</div>
           </vue-custom-scrollbar>
				</div>
			</div>
			<!-- /Sidebar -->
</template>

<script type="text/babel">
import vueCustomScrollbar from 'vue-custom-scrollbar'
import "vue-custom-scrollbar/dist/vueScrollbar.css"
import { HomeIcon } from 'vue-feather-icons'
import { UsersIcon } from 'vue-feather-icons'
import { FileTextIcon } from 'vue-feather-icons'
import { ClipboardIcon } from 'vue-feather-icons'
import { CreditCardIcon } from 'vue-feather-icons'
import { PackageIcon } from 'vue-feather-icons'
import { SettingsIcon } from 'vue-feather-icons'
import { UserIcon } from 'vue-feather-icons'
import { BarChartIcon } from 'vue-feather-icons'
import {
  StarIcon,
  DocumentIcon,
  DocumentTextIcon,
  CalculatorIcon,
  ChartBarIcon,
  CogIcon,
} from '@vue-hero-icons/outline'
import { mapGetters, mapActions } from 'vuex'

export default {
  components: {
    HomeIcon,
    UserIcon,
    StarIcon,
    DocumentIcon,
    DocumentTextIcon,
    CreditCardIcon,
    CalculatorIcon,
    ChartBarIcon,
    CogIcon,
    UsersIcon,
    HomeIcon,
    UsersIcon,
    FileTextIcon,
    ClipboardIcon,
    CreditCardIcon,
    PackageIcon,
    SettingsIcon,
    UserIcon,
    vueCustomScrollbar,
    BarChartIcon
  },

  computed: {
    ...mapGetters(['isSidebarOpen']),
    ...mapGetters('user', ['currentUser']),
    menuItems() {
      let menu = [
        [
          {
            title: 'navigation.dashboard',
            icon: 'home-icon',
            route: '/admin/dashboard',
          },
          {
            title: 'navigation.customers',
            icon: 'user-icon',
            route: '/admin/customers',
          },
          {
            title: 'navigation.items',
            icon: 'star-icon',
            route: '/admin/items',
          },
        ],
        [
          {
            title: 'navigation.estimates',
            icon: 'document-icon',
            route: '/admin/estimates',
          },
          {
            title: 'navigation.invoices',
            icon: 'document-text-icon',
            route: '/admin/invoices',
          },
          {
            title: 'navigation.payments',
            icon: 'credit-card-icon',
            route: '/admin/payments',
          },
          {
            title: 'navigation.expenses',
            icon: 'calculator-icon',
            route: '/admin/expenses',
          },
        ],
        [
          {
            title: 'navigation.reports',
            icon: 'chart-bar-icon',
            route: '/admin/reports',
          },
          {
            title: 'navigation.settings',
            icon: 'cog-icon',
            route: '/admin/settings',
          },
        ],
      ]

      if (this.currentUser.role == 'super admin') {
        menu[2] = [
          {
            title: 'navigation.users',
            icon: 'users-icon',
            route: '/admin/users',
          },
          ...menu[2],
        ]
      }

      return menu
    },
    currentPath() {
        return this.$route.path;
    },
    currentPathName() {
      return this.$route.name
    },
  },
 data() {
      return {
        settings: {
          suppressScrollX: true,
        },
      };
              //  isactive : true
  },
  mounted() {
    let $wrapper = $('.main-wrapper');

    $('#sidebar-menu a').on('click', function (e) {
      if ($(this).parent().hasClass('submenu')) {
        e.preventDefault();
      }
      else {
          $wrapper.removeClass('slide-nav');
          $(".sidebar-overlay").removeClass("opened");
          $('html').removeClass('menu-opened');
      }
      if (!$(this).hasClass('subdrop')) {
        $('ul', $(this).parents('ul:first')).slideUp(350);
        $('a', $(this).parents('ul:first')).removeClass('subdrop');
        $(this).next('ul').slideDown(350);
        $(this).addClass('subdrop');
        } else if ($(this).hasClass('subdrop')) {
        $(this).removeClass('subdrop');
        $(this).next('ul').slideUp(350);
      }
    });
    $('#sidebar-menu ul li.submenu a.active').parents('li:last').children('a:first').addClass('active').trigger('click');
  },
  methods: {
    scrollHanle(evt) {
      // console.log(evt)
    },
    ...mapActions(['toggleSidebar']),

    hasActiveUrl(url) {
      this.isActive = true
      return this.$route.path.indexOf(url) > -1
    },
    hasStaticUrl(url) {
      return this.$route.path.indexOf(url)
    },
  },
}
</script>
<style>
    .scroll-area {
      position: relative;
      margin: auto;
      height: calc(100vh - 60px);
      background-color: transparent !important;
  }
</style>