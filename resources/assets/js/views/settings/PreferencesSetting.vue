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
                  <h5 class="card-title">{{ $t('settings.menu_title.preferences') }}</h5>
                  <!-- <p>Default preferences for the system</p> -->
                </div>
                <div class="card-body">
                
                  <!-- Form -->
                  <form @submit.prevent="updatePreferencesData" >
                    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
                    <div class="form-group row">
                          <label class="col-lg-3 col-form-label">{{$tc('settings.preferences.currency')}} <span class="text-danger">*</span></label>
                          <div class="col-lg-9">

                            <select class="select form-control" v-model="formData.currency">
                              <option>{{$tc('settings.currencies.select_currency')}}</option>
                              <option v-for="item in currencies" :key="item.id" v-bind:value="item.id">{{item.name}}</option>
                            </select>
                          </div>
                        </div>
                         <div class="form-group row">
                          <label class="col-lg-3 col-form-label">{{$tc('settings.preferences.time_zone')}} <span class="text-danger">*</span></label>
            
                          <div class="col-lg-9">
                            <select class="select form-control" v-model="formData.timeZone">
                              <option>{{$tc('settings.preferences.select_time_zone')}}</option>
                              <option v-for="item in timeZones" :key="item.value" :value="item.value">{{item.key}}</option>
                            </select>
                          </div>
                        </div>
                         <div class="form-group row">
                          <label class="col-lg-3 col-form-label">{{$tc('settings.preferences.fiscal_year')}} <span class="text-danger">*</span></label>
                          <div class="col-lg-9">
                            <select class="select form-control"  v-model="formData.fiscalYear">
                              <option>{{$tc('settings.preferences.select_financial_year')}}</option>
                              <option v-for="item in fiscalYears" :key="item.value" v-bind:value="item.value">{{item.key}}</option>
                            </select>
                          </div>
                        </div>
                        
                       
                       <div class="form-group row">
                          <label class="col-lg-3 col-form-label">{{$tc('settings.preferences.default_language')}} <span class="text-danger">*</span></label>
                          <div class="col-lg-9">
                            <select class="select form-control" v-model="formData.language">
                              <option>{{$tc('settings.preferences.select_language')}}</option>
                              <option v-for="item in languages" :key="item.code" :value="item.code">{{item.name}}</option>
                            </select>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label">{{$tc('settings.preferences.date_format')}} <span class="text-danger">*</span></label>
                          <div class="col-lg-9">
                            <select class="select form-control" v-model="formData.dateFormat" @change="changeDate">
                              <option>{{$tc('settings.preferences.select_date_format')}}</option>
                              <option v-for="item in dateFormats" :key="item.carbon_format_value" :value="item.carbon_format_value">{{item.display_date}}</option>
                            </select>
                          </div>
                        </div>
                    
                  
                    <div class="text-left">
                      <button type="submit"  :disabled="isLoading" :loading="isLoading" class="btn btn-primary">{{ $tc('settings.company_info.save') }}</button>
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
import { mapActions, mapGetters } from 'vuex'
const { required } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      formData: {
        language: null,
        currency: null,
        timeZone: null,
        dateFormat: null,
        fiscalYear: null,
        carbonMomentFormat: null,
      },
      defaultCurrencyInput: null,
      isRequestOnGoing: false,
      discount_per_item: null,
    }
  },

  validations: {
    formData: {
      currency: {
        required,
      },
      language: {
        required,
      },
      dateFormat: {
        required,
      },
      timeZone: {
        required,
      },
      fiscalYear: {
        required,
      },
    },
  },

  computed: {
    ...mapGetters([
      'currencies',
      'timeZones',
      'dateFormats',
      'fiscalYears',
      'languages',
    ]),

    ...mapGetters('company', ['defaultFiscalYear', 'defaultTimeZone']),

    currencyError() {
      if (!this.$v.formData.currency.$error) {
        return ''
      }
      if (!this.$v.formData.currency.required) {
        return this.$tc('validation.required')
      }
    },

    languageError() {
      if (!this.$v.formData.language.$error) {
        return ''
      }
      if (!this.$v.formData.language.required) {
        return this.$tc('validation.required')
      }
    },

    timeZoneError() {
      if (!this.$v.formData.timeZone.$error) {
        return ''
      }
      if (!this.$v.formData.timeZone.required) {
        return this.$tc('validation.required')
      }
    },

    fiscalYearError() {
      if (!this.$v.formData.fiscalYear.$error) {
        return ''
      }
      if (!this.$v.formData.fiscalYear.required) {
        return this.$tc('settings.company_info.errors.required')
      }
    },

    dateFormatError() {
      if (!this.$v.formData.dateFormat.$error) {
        return ''
      }

      if (!this.$v.formData.dateFormat.required) {
        return this.$tc('validation.required')
      }
    },
  },

  async mounted() {
    this.setInitialData()
  },
  watch: {
    'formData.currency': function(newValue) {
      if(newValue) {
           this.defaultCurrencyInput =   this.currencies.find(
          (currency) => currency.id == newValue
        )
      }
     
    }
  },
  methods: {
    ...mapActions('company', [
      'setDefaultCurrency',
      'fetchCompanySettings',
      'updateCompanySettings',
    ]),

    ...mapActions([
      'fetchLanguages',
      'fetchFiscalYears',
      'fetchDateFormats',
      'fetchTimeZones',
    ]),

    currencyNameWithCode({ name, code }) {
      return `${code} - ${name}`
    },

    changeDate() {
        this.formData.carbonMomentFormat = this.dateFormats.find(
          (dateFormat) =>  dateFormat.carbon_format_value == this.formData.dateFormat)
    },  
    
    async setInitialData() {
      this.isRequestOnGoing = true

      await this.fetchDateFormats()
      await this.fetchLanguages()
      await this.fetchFiscalYears()
      await this.fetchTimeZones()

      let response = await this.fetchCompanySettings([
        'currency',
        'time_zone',
        'language',
        'fiscal_year',
        'carbon_date_format',
        'moment_date_format',
        'discount_per_item',
      ])

      if (response.data) {
        response.data.discount_per_item === 'YES'
          ? (this.discount_per_item = true)
          : (this.discount_per_item = false)

        this.formData.currency = response.data.currency &&  this.currencies && this.currencies.find(
          (currency) => currency.id == response.data.currency
        ).id

        this.defaultCurrencyInput = response.data.currency &&  this.currencies && this.currencies.find(
          (currency) => currency.id == response.data.currency
        )

        this.formData.language = response.data.language &&  this.languages && this.languages.find(
          (language) => language.code == response.data.language
        ).code
        
        this.formData.timeZone = this.defaultTimeZone && this.timeZones && this.timeZones.find(
          (timeZone) => timeZone.value == this.defaultTimeZone
        ).value

        this.formData.fiscalYear = this.fiscalYears && this.defaultFiscalYear && this.fiscalYears.find(
          (fiscalYear) => fiscalYear.value == this.defaultFiscalYear
        ).value

        this.formData.dateFormat =response.data.carbon_date_format &&  this.dateFormats && this.dateFormats.find(
          (dateFormat) =>
            dateFormat.carbon_format_value == response.data.carbon_date_format
        ).carbon_format_value

        this.formData.carbonMomentFormat = this.dateFormats && response.data.carbon_date_format && this.dateFormats.find(
          (dateFormat) =>
            dateFormat.carbon_format_value == response.data.carbon_date_format
        )
      }

      this.isRequestOnGoing = false
    },

    async updatePreferencesData() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let data = {
        settings: {
          currency: this.formData.currency,
          time_zone: this.formData.timeZone,
          fiscal_year: this.formData.fiscalYear,
          language: this.formData.language,
          carbon_date_format: this.formData.carbonMomentFormat.carbon_format_value,
          moment_date_format: this.formData.carbonMomentFormat.moment_format_value,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) { 
        this.isLoading = false
        // window.i18n.locale = this.formData.language.code
        this.setDefaultCurrency(this.defaultCurrencyInput)
        window.toastr['success'](
          this.$t('settings.preferences.updated_message')
        )
        return true
      }
      window.toastr['error'](response.data.error)
      return true
    },

    async setDiscount() {
      let data = {
        settings: {
          discount_per_item: this.discount_per_item ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
  },
}
</script>
