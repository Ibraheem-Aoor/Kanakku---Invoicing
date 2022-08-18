<template>
  <div>
    <h3>{{$t('wizard.preferences')}}</h3>
    <div>{{$t('wizard.preferences_desc')}}</div>
    <base-loader v-if="isFetching" :show-bg-overlay="true" />
    <form  @submit.prevent="next">
      <div>
        <div class="row mt-4">
          <div class="form-group col-md-6">
            <label>{{$t('wizard.currency')}}</label>
            <select
              v-model="settingData.currency"
              :class="{ 'is-invalid': $v.settingData.currency.$error }"
              class="form-control"
              @change="$v.settingData.currency.$touch()"
            >
            <option value="">{{$t('settings.currencies.select_currency')}}</option>
            <option v-for="item in currencies" :key="item.id" :value="item.id">{{item.name}}</option> 
            </select>
            <div class="invalid-feedback" v-if="$v.settingData.currency.$error">{{currencyError}}</div>
          </div>
 
          <div class="form-group col-md-6">
            <label>{{$t('settings.preferences.default_language')}}</label>
            <select
              v-model="settingData.language"
              :class="{ 'is-invalid': $v.settingData.language.$error }"
              class="form-control"
              label="name"
            > 
            <option value="">{{$t('settings.preferences.select_language')}}</option>
            <option v-for="item in languages" :key="item.id" :value="item.code">{{item.name}}</option> 
            </select>
            <div class="invalid-feedback" v-if="$v.settingData.language.$error">{{languageError}}</div>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-6">
            <label>{{$t('wizard.date_format')}}</label>
            <select
              v-model="settingData.dateFormat"
              :class="{ 'is-invalid': $v.settingData.dateFormat.$error }"
              class="form-control"
              label="display_date" 
            > 
            <option value="">{{$t('settings.preferences.select_date_format')}}</option>
            <option v-for="item in dateFormats" :key="item.carbon_format_value" :value="item.carbon_format_value">{{item.carbon_format_value}}</option>
            </select>
            <div class="invalid-feedback" v-if="$v.settingData.dateFormat.$error">{{dateFormatError}}</div>
          </div>

          <div class="form-group col-md-6">
            <label>{{$t('wizard.time_zone')}}</label>
            <select
              v-model="settingData.timeZone"
              :class="{ 'is-invalid': $v.settingData.timeZone.$error }"
              class="form-control"
              label="key"
              @change="changeDate"
            > 
            <option value="">{{$tc('settings.preferences.select_time_zone')}}</option>
            <option v-for="item in timeZones" :key="item.value" :value="item.value">{{item.key}}</option>
            </select>
            <div class="invalid-feedback" v-if="$v.settingData.timeZone.$error">{{timeZoneError}}</div>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-6">
          <label>{{$t('wizard.fiscal_year')}}</label>
            <select
              v-model="settingData.fiscalYear"
              :class="{ 'is-invalid': $v.settingData.fiscalYear.$error }"
              class="form-control"
              label="key"
            > 
            <option value="">{{$tc('settings.preferences.select_financial_year')}}</option>
            <option v-for="item in fiscalYears" :key="item.value" :value="item.value">{{item.key}}</option>
            </select>
            <div class="invalid-feedback" v-if="$v.settingData.fiscalYear.$error">{{fiscalYearError}}</div>
          </div>
        </div>
        <button
          :loading="isLoading"
          :disabled="isLoading"
          class="btn btn-primary mt-4"
          variant="primary"
          type="submit"
        >
          {{ $t('wizard.save_cont') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import Ls from '../../services/ls'
import { mapActions, mapGetters } from 'vuex'

const { required, minLength, email } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      settingData: {
        language: null,
        currency: null,
        timeZone: null,
        dateFormat: null,
        fiscalYear: null,
        carbonMomentFormat: null,
      },
      isLoading: false,
      isFetching: false,
      step: 1,
    }
  },
  props: {
    selectedTab: {
      type: Number,
      require: true,
      default: Number,
    },
  },
  validations: {
    settingData: {
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
      'languages',
      'currencies',
      'timeZones',
      'dateFormats',
      'fiscalYears',
    ]),

    ...mapGetters('company', ['defaultFiscalYear', 'defaultTimeZone']),
    currencyError() {
      if (!this.$v.settingData.currency.$error) {
        return ''
      }
      if (!this.$v.settingData.currency.required) {
        return this.$tc('validation.required')
      }
    },
    languageError() {
      if (!this.$v.settingData.language.$error) {
        return ''
      }

      if (!this.$v.settingData.language.required) {
        return this.$tc('validation.required')
      }
    },
    dateFormatError() {
      if (!this.$v.settingData.dateFormat.$error) {
        return ''
      }
      if (!this.$v.settingData.dateFormat.required) {
        return this.$tc('validation.required')
      }
    },
    timeZoneError() {
      if (!this.$v.settingData.timeZone.$error) {
        return ''
      }
      if (!this.$v.settingData.timeZone.required) {
        return this.$tc('validation.required')
      }
    },
    fiscalYearError() {
      if (!this.$v.settingData.fiscalYear.$error) {
        return ''
      }
      if (!this.$v.settingData.fiscalYear.required) {
        return this.$tc('validation.required')
      }
    },
  },
   watch: {
    'settingData.currency': function(newValue) {
      if(newValue) {
           this.settingData.currency =   this.currencies && this.currencies.find(
          (currency) => currency.id == newValue
        ).id
      }
    }
  },
  mounted() {
    // this.getOnboardingData()
    this.setInitialData()
  },
  methods: {
    ...mapActions('company', ['updateCompanySettings', 'setSelectedCompany']),
    ...mapActions([
      'fetchLanguages',
      'fetchCurrencies',
      'fetchFiscalYears',
      'fetchDateFormats',
      'fetchTimeZones',
    ]),
     changeDate() {
        this.settingData.carbonMomentFormat = this.dateFormats.find(
          (dateFormat) =>  dateFormat.carbon_format_value == this.settingData.dateFormat)
    },
    async setInitialData() {
      this.isFetching = true
      await this.fetchCurrencies()
      await this.fetchDateFormats()
      await this.fetchLanguages()
      await this.fetchFiscalYears()
      await this.fetchTimeZones()
      await this.fetchLanguages()
     
      this.settingData.carbonMomentFormat = this.dateFormats && this.dateFormats.find(
          (dateFormat) =>
            dateFormat.carbon_format_value == 'd M Y'
      )

      this.settingData.currency = this.currencies && this.currencies.find(
        (currency) => currency.id === 1
      ).id
      
      this.settingData.language = this.languages && this.languages.find(
        (language) => language.code === 'en'
      ).code
      this.settingData.dateFormat = this.dateFormats && this.dateFormats.find(
        (dateFormat) => dateFormat.carbon_format_value == 'd M Y'
      ).carbon_format_value

      this.settingData.timeZone = this.timeZones && this.timeZones.find(
        (timeZone) => timeZone.value === 'UTC'
      ).value

      this.settingData.fiscalYear = this.fiscalYears && this.fiscalYears.find(
        (fiscalYear) => fiscalYear.value === '1-12'
      ).value
      this.isFetching = false
    },
    currencyNameWithCode({ name, code }) {
      return `${code} - ${name}`
    },
    async next() {
      this.$v.settingData.$touch()

      if (this.$v.settingData.$invalid) {
        return true
      }

      this.isLoading = true
      let data = {
        settings: {
          currency: this.settingData.currency,
          time_zone: this.settingData.timeZone,
          language: this.settingData.language,
          fiscal_year: this.settingData.fiscalYear,
          carbon_date_format: this.settingData.carbonMomentFormat.carbon_format_value,
          moment_date_format: this.settingData.carbonMomentFormat.moment_format_value,
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        this.isLoading = false
        this.updateUserSettings()
        Ls.set('auth.token', response.data.token)
      }
    },
    async updateUserSettings() {
      let data = {
        settings: {
          language: this.settingData.language,
        },
      }

      let response = await axios.put('/api/v1/me/settings', data)

      if (response.data) {
        this.$emit('next', 'COMPLETED')
        window.toastr['success']('Login Successful')
        this.$router.push('/admin/dashboard')
      }
    },
  },
}
</script>
