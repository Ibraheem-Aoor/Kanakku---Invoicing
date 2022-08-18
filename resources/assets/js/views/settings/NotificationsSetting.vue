<template>
 <base-page>
   
   
            <div class="page-header">
          <div class="row">
            <div class="col-sm-6">
              <h3 class="page-title">{{ $t('navigation.settings') }}</h3>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/admin/dashboard">{{ $t('general.home') }}</router-link>
                </li>
                <li class="breadcrumb-item active">{{ $t('navigation.settings') }}</li>
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
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title">{{ $t('settings.notification.title') }}</h5>
                  <p>{{ $t('settings.notification.description') }}</p>
                </div>
                <div class="card-body">
                
                  <!-- Form -->
                  <form @submit.prevent="saveEmail()">
                    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
                    <div class="row form-group">
                      <label for="notificationmail" class="col-sm-3 col-form-label input-label">{{$t('settings.notification.email')}} <span class="text-danger">*</span></label>
                      <div class="col-sm-9">
                        <input type="email" class="form-control" :class="{ 'is-invalid': $v.notification_email.$error }"  v-model.trim="notification_email" :placeholder="$tc('settings.notification.please_enter_email')" @input="$v.notification_email.$touch()" id="notificationmail">
                        <div class="invalid-feedback">{{notificationEmailError}}</div>
                      </div>
                    </div>


                    <!-- Toggle Switch -->
                    <label class="row form-group toggle-switch" for="notification_switch1">
                      <span class="col-8 col-sm-9 toggle-switch-content ml-0">
                        <span class="d-block text-dark">{{ $t('settings.notification.invoice_viewed') }}</span>
                        <span class="d-block text-muted">{{ $t('settings.notification.invoice_viewed_desc') }}</span>
                      </span>
                      <span class="col-4 col-sm-3">
                        <input type="checkbox"  v-model="notify_invoice_viewed" @change="setInvoiceViewd" class="toggle-switch-input" id="notification_switch1">
                        <span class="toggle-switch-label ml-auto">
                          <span class="toggle-switch-indicator"></span>
                        </span>
                      </span>
                    </label>
                    <!-- /Toggle Switch -->

                    <!-- Toggle Switch -->
                    <label class="row form-group toggle-switch" for="notification_switch2">
                      <span class="col-8 col-sm-9 toggle-switch-content ml-0">
                        <span class="d-block text-dark">{{ $t('settings.notification.estimate_viewed') }}</span>
                        <span class="d-block text-muted">{{ $t('settings.notification.estimate_viewed_desc') }}</span>
                      </span>
                      <span class="col-4 col-sm-3">
                        <input type="checkbox" class="toggle-switch-input" v-model="notify_estimate_viewed" @change="setEstimateViewd" id="notification_switch2">
                        <span class="toggle-switch-label ml-auto">
                          <span class="toggle-switch-indicator"></span>
                        </span>
                      </span>
                    </label>
                    <!-- /Toggle Switch -->
                    
                    <div class="text-right">
                      <button type="submit" class="btn btn-primary">{{ $tc('settings.notification.save') }}</button>
                    </div>
                  </form>
                  <!-- /Form -->
                </div>
              </div>
            </div>
                  </form>
                  <!-- /Form -->
                  
                </div>
              </div>
            </div>
          </div>
    
    </base-page>
</template>

<script>
import { mapActions } from 'vuex'
const { required, email } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      notification_email: null,
      notify_invoice_viewed: null,
      notify_estimate_viewed: null,
      isRequestOnGoing: false,
    }
  },
  validations: {
    notification_email: {
      required,
      email,
    },
  },
  computed: {
    notificationEmailError() {
      if (!this.$v.notification_email.$error) {
        return ''
      }

      if (!this.$v.notification_email.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.notification_email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    ...mapActions('company', ['fetchCompanySettings', 'updateCompanySettings']),

    async fetchData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCompanySettings([
        'notify_invoice_viewed',
        'notify_estimate_viewed',
        'notification_email',
      ])

      if (response.data) {
        this.notification_email = response.data.notification_email

        response.data.notify_invoice_viewed === 'YES'
          ? (this.notify_invoice_viewed = true)
          : (this.notify_invoice_viewed = false)

        response.data.notify_estimate_viewed === 'YES'
          ? (this.notify_estimate_viewed = true)
          : (this.notify_estimate_viewed = false)
      }
      this.isRequestOnGoing = false
    },

    async saveEmail() {
      this.$v.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      let data = {
        settings: {
          notification_email: this.notification_email,
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data.success) {
        this.isLoading = false

        window.toastr['success'](
          this.$tc('settings.notification.email_save_message')
        )
      }
    },

    async setInvoiceViewd(val) {
      this.$v.$touch()

      if (this.$v.$invalid) {
        this.notify_invoice_viewed = !this.notify_invoice_viewed
        return true
      }

      let data = {
        settings: {
          notify_invoice_viewed: this.notify_invoice_viewed ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data.success) {
        window.toastr['success'](this.$tc('general.setting_updated'))
      }
    },

    async setEstimateViewd(val) {
      this.$v.$touch()

      if (this.$v.$invalid) {
        this.notify_estimate_viewed = !this.notify_estimate_viewed
        return true
      }

      let data = {
        settings: {
          notify_estimate_viewed: this.notify_estimate_viewed ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        window.toastr['success'](this.$tc('general.setting_updated'))
      }
    },
  },
}
</script>
