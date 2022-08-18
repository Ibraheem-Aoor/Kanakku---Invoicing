<template>
 <base-page>
    
    <div class="page-header">
            <div class="row">
              <div class="col-sm-6">
                <h3 class="page-title">Settings</h3>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><router-link to="/admin/dashboard">Dashboard</router-link>
                  </li>
                  <li class="breadcrumb-item active">Profile Settings</li>
                </ul>
              </div>
            </div>
          </div>
        
          <div class="row">
           <div class="col-xl-3 col-md-4">
            
              <!-- Settings Menu -->
                <div class="widget settings-menu">
                <ul>
                  <li class="nav-item">
                    <router-link to="/admin/settings/user-profile" class="nav-link">
                      <i class="far fa-user"></i> <span>{{ $t('settings.account_settings.account_settings') }}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/company-info" class="nav-link">
                      <i class="far fa-list-alt"></i> <span>{{$t('settings.menu_title.company_information')}}</span>
                    </router-link>
                  </li>
                    <li class="nav-item">
                    <router-link to="/admin/settings/customization" class="nav-link">
                      <i class="fas fa-edit"></i> <span>{{$t('settings.menu_title.customization')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/preferences" class="nav-link">
                      <i class="fas fa-cog"></i> <span>{{$t('settings.menu_title.preferences')}}</span>
                    </router-link>
                  </li>
                  
                   <li class="nav-item">
                    <router-link to="/admin/settings/notifications" class="nav-link">
                      <i class="far fa-bell"></i> <span>{{$t('settings.menu_title.notifications')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/tax-types" class="nav-link">
                      <i class="far fa-check-square"></i> <span>{{$t('settings.menu_title.tax_types')}}</span>
                    </router-link>
                  </li>
                   <li class="nav-item">
                    <router-link to="/admin/settings/payment-mode" class="nav-link">
                      <i class="far fa-credit-card"></i> <span>{{$t('settings.menu_title.payment_modes')}}</span>
                    </router-link>
                  </li>
                   <li class="nav-item">
                    <router-link to="/admin/settings/custom-fields" class="nav-link">
                      <i class="far fa-bell"></i> <span>{{$t('settings.menu_title.custom_fields')}}</span>
                    </router-link>
                  </li>
                  
                  <li class="nav-item">
                    <router-link to="/admin/settings/expense-category" class="nav-link">
                      <i class="far fa-list-alt"></i> <span>{{$t('settings.menu_title.expense_category')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/mail-configuration" class="nav-link">
                      <i class="far fa-check-square"></i> <span>{{$t('settings.mail.mail_config')}}</span>
                    </router-link>
                  </li>
                </ul>
              </div>
              <!-- /Settings Menu -->
              
            </div>
            
               <div class="col-xl-9 col-md-8 mt-3">
                  <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title">{{ $t('settings.mail.mail_config') }}</h5>
                  <p>{{ $t('settings.mail.mail_config_desc') }}</p>
                </div>
                <div class="card-body">
                  <div v-if="mailConfigData">
                      <component
                        :is="mail_driver"
                        :config-data="mailConfigData"
                        :loading="isLoading"
                        :mail-drivers="mail_drivers"
                        @on-change-driver="
                          (val) => (mail_driver = mailConfigData.mail_driver = val)
                        "
                        @submit-data="saveEmailConfig"
                      >
                        <button
                          variant="primary-outline"
                          type="button"
                          class="ml-2 btn btn-primary"  
                          @click="openMailTestModal"
                        >
                          {{ $t('general.test_mail_conf') }}
                        </button>
                      </component>
                    </div>
                </div>
                
              </div>
            <MailTestModal  ref="modal"/>
          </div>
    </div>
    </base-page>
</template>

<script>
import { mapActions } from 'vuex'
import SmtpMailDriver from './mail-driver/SmtpMailDriver.vue'
import BasicMailDriver from './mail-driver/BasicMailDriver.vue'
import MailTestModal from '../../components/base/modal/MailTestModal.vue'
import MailgunMailDriver from './mail-driver/MailgunMailDriver.vue'
import SesMailDriver from './mail-driver/SesMailDriver.vue'
export default {

  data() {
    return {
      mailConfigData: null,
      mail_driver: 'smtp',
      isLoading: false,
      isRequestOnGoing: false,
      mail_drivers: [],
    }
  },
  components: {
    'smtp': SmtpMailDriver,
    'mail': BasicMailDriver,
    'sendmail': BasicMailDriver,
    'mailgun': MailgunMailDriver,
    'ses': SesMailDriver,
    MailTestModal,
  },
  mounted() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('company', [
      'fetchMailDrivers',
      'fetchMailConfig',
      'updateMailConfig',
    ]),

    async loadData() {
      this.isRequestOnGoing = true
      let mailDrivers = await this.fetchMailDrivers()

      let mailData = await this.fetchMailConfig()

      if (mailDrivers.data) {
        this.mail_drivers = mailDrivers.data
      }

      if (mailData.data) {
        this.mailConfigData = mailData.data
        this.mail_driver = mailData.data.mail_driver
      }
      this.isRequestOnGoing = false
    },

    async saveEmailConfig(mailConfigData) {
      try {
        this.isLoading = true
        let response = await this.updateMailConfig(mailConfigData)
        if (response.data.success) {
          this.isLoading = false
          window.toastr['success'](
            this.$t('wizard.success.' + response.data.success)
          )
        } else {
          window.toastr['error'](
            this.$t('wizard.errors.' + response.data.error)
          )
        }
        return true
      } catch (e) {
        window.toastr['error']('Something went wrong')
      }
    },

    openMailTestModal() {
      this.$refs.modal.show()
    },
  },
}
</script>
