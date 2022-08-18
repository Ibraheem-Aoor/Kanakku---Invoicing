<template>
  <base-page>
      <div class="col-span-12 xl:col-span-9">
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div>
  </base-page>
</template>

<script>
import {
  UserIcon,
  OfficeBuildingIcon,
  BellIcon,
  CheckCircleIcon,
  ClipboardListIcon,
  CubeIcon,
  ClipboardCheckIcon,
} from '@vue-hero-icons/outline'

import {
  RefreshIcon,
  CogIcon,
  MailIcon,
  PencilAltIcon,
  CloudUploadIcon,
  FolderIcon,
  DatabaseIcon,
  CreditCardIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    UserIcon,
    OfficeBuildingIcon,
    PencilAltIcon,
    CogIcon,
    CheckCircleIcon,
    ClipboardListIcon,
    MailIcon,
    BellIcon,
    FolderIcon,
    RefreshIcon,
    CubeIcon,
    CloudUploadIcon,
    DatabaseIcon,
    CreditCardIcon,
    ClipboardCheckIcon,
  },

  data() {
    return {
      currentSetting: {
        link: '/admin/settings/user-profile',
        title: 'settings.menu_title.account_settings',
        icon: 'user-icon',
      },
      menuItems: [
        {
          link: '/admin/settings/user-profile',
          title: 'settings.menu_title.account_settings',
          icon: 'user-icon',
        },
        {
          link: '/admin/settings/company-info',
          title: 'settings.menu_title.company_information',
          icon: 'office-building-icon',
        },
        {
          link: '/admin/settings/preferences',
          title: 'settings.menu_title.preferences',
          icon: 'cog-icon',
        },
        {
          link: '/admin/settings/customization',
          title: 'settings.menu_title.customization',
          icon: 'pencil-alt-icon',
        },
        {
          link: '/admin/settings/notifications',
          title: 'settings.menu_title.notifications',
          icon: 'bell-icon',
        },
        {
          link: '/admin/settings/tax-types',
          title: 'settings.menu_title.tax_types',
          icon: 'check-circle-icon',
        },
        {
          link: '/admin/settings/payment-mode',
          title: 'settings.menu_title.payment_modes',
          icon: 'credit-card-icon',
        },
        {
          link: '/admin/settings/custom-fields',
          title: 'settings.menu_title.custom_fields',
          icon: 'cube-icon',
        },
        {
          link: '/admin/settings/notes',
          title: 'settings.menu_title.notes',
          icon: 'clipboard-check-icon',
        },
        {
          link: '/admin/settings/expense-category',
          title: 'settings.menu_title.expense_category',
          icon: 'clipboard-list-icon',
        },

        {
          link: '/admin/settings/mail-configuration',
          title: 'settings.mail.mail_config',
          icon: 'mail-icon',
        },
        {
          link: '/admin/settings/file-disk',
          title: 'settings.menu_title.file_disk',
          icon: 'folder-icon',
        },
        {
          link: '/admin/settings/backup',
          title: 'settings.menu_title.backup',
          icon: 'database-icon',
        },
        {
          link: '/admin/settings/update-app',
          title: 'settings.menu_title.update_app',
          icon: 'refresh-icon',
        },
      ],
    }
  },

  watch: {
    '$route.path'(newValue) {
      if (newValue === '/admin/settings') {
        this.$router.push('/admin/settings/user-profile')
      }
    },
  },

  mounted() {
    this.currentSetting = this.menuItems.find(
      (item) => item.link == this.$route.path
    )
  },

  created() {
    if (this.$route.path === '/admin/settings') {
      this.$router.push('/admin/settings/user-profile')
    }
  },

  methods: {
    getCustomLabel({ title }) {
      return this.$t(title)
    },
    hasActiveUrl(url) {
      return this.$route.path.indexOf(url) > -1
    },
    navigateToSetting(setting) {
      this.$router.push(setting.link)
    },
  },
}
</script>
