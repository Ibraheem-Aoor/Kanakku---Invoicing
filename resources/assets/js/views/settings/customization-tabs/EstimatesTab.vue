
<template>
  <div class="card card-body" v-if="!isLoading">
    <form action="" class="mt-6" @submit.prevent="updateEstimateSetting">
      <div class="form-group">
          <label>{{$t('settings.customization.estimates.estimate_prefix')}}</label>
          <input type="text" class="form-control" v-model="estimates.estimate_prefix"
          :class="{'is-invalid': $v.estimates.estimate_prefix.$error}"
          style="max-width: 30%"
          @input="$v.estimates.estimate_prefix.$touch()"
          @keyup="changeToUppercase('ESTIMATES')">
      </div>
       <div class="form-group">
         <label>{{$t('settings.customization.estimates.default_estimate_email_body')}}</label>
          <vue-editor v-model="estimates.estimate_mail_body" />
          <base-custom-input
          v-model="estimates.estimate_mail_body"
          :fields="mailFields"
          class="mt-2"
        />
       </div>
        <div class="form-group">
         <label>{{$t('settings.customization.estimates.company_address_format')}}</label>
          <vue-editor v-model="estimates.company_address_format" />
          <base-custom-input
          v-model="estimates.company_address_format"
          :fields="companyFields"
          class="mt-2"
        />
       </div>
         <div class="form-group">
         <label>{{$t('settings.customization.estimates.shipping_address_format')}}</label>
          <vue-editor v-model="estimates.shipping_address_format" />
          <base-custom-input
          v-model="estimates.shipping_address_format"
          :fields="shippingFields"
          class="mt-2"
        />
       </div>
         <div class="form-group">
         <label>{{$t('settings.customization.estimates.billing_address_format')}}</label>
          <vue-editor v-model="estimates.billing_address_format" />
          <base-custom-input
          v-model="estimates.billing_address_format"
          :fields="billingFields"
          class="mt-2"
        />
       </div>
      <button
        :disabled="isLoading"
        type="submit"
        class="btn btn-primary mt-4"
      >
       <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
        {{ $t('settings.customization.save') }}
      </button>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import {VueEditor} from 'vue2-editor'

export default {
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
    },
  },
  components: {
    VueEditor
  },
  data() {
    return {
      estimateAutogenerate: false,

      estimates: {
        estimate_prefix: null,
        estimate_mail_body: null,
        estimate_terms_and_conditions: null,
        company_address_format: null,
        shipping_address_format: null,
        billing_address_format: null,
      },
      billingFields: [
        'billing',
        'customer',
        'customerCustom',
        'estimateCustom',
      ],
      shippingFields: [
        'shipping',
        'customer',
        'customerCustom',
        'estimateCustom',
      ],
      mailFields: [
        'customer',
        'estimate',
        'company',
        'customerCustom',
        'estimateCustom',
      ],
      companyFields: ['company', 'estimateCustom'],
      isLoading: false,
    }
  },

  computed: {
    estimatePrefixError() {
      if (!this.$v.estimates.estimate_prefix.$error) {
        return ''
      }

      if (!this.$v.estimates.estimate_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.estimates.estimate_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.estimates.estimate_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
  },

  validations: {
    estimates: {
      estimate_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
  },

  watch: {
    settings(val) {
      this.estimates.estimate_prefix = val ? val.estimate_prefix : ''

      this.estimates.estimate_mail_body = val ? val.estimate_mail_body : ''
      this.estimates.company_address_format = val
        ? val.estimate_company_address_format
        : ''
      this.estimates.shipping_address_format = val
        ? val.estimate_shipping_address_format
        : ''
      this.estimates.billing_address_format = val
        ? val.estimate_billing_address_format
        : ''

      this.estimates.estimate_terms_and_conditions = val
        ? val.estimate_terms_and_conditions
        : ''

      this.estimate_auto_generate = val ? val.estimate_auto_generate : ''

      if (this.estimate_auto_generate === 'YES') {
        this.estimateAutogenerate = true
      } else {
        this.estimateAutogenerate = false
      }
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),

    async setEstimateSetting() {
      let data = {
        settings: {
          estimate_auto_generate: this.estimateAutogenerate ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    changeToUppercase(currentTab) {
      if (currentTab === 'ESTIMATES') {
        this.estimates.estimate_prefix = this.estimates.estimate_prefix.toUpperCase()
        return true
      }
    },

    async updateEstimateSetting() {
      this.$v.estimates.$touch()

      if (this.$v.estimates.$invalid) {
        return false
      }

      let data = {
        settings: {
          estimate_prefix: this.estimates.estimate_prefix,
          estimate_mail_body: this.estimates.estimate_mail_body,
          estimate_company_address_format: this.estimates
            .company_address_format,
          estimate_shipping_address_format: this.estimates
            .shipping_address_format,
          estimate_billing_address_format: this.estimates
            .billing_address_format,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.customization.estimates.estimate_setting_updated')
        )
      }
    },

    async updateSetting(data) {
      this.isLoading = true
      let res = await this.updateCompanySettings(data)

      if (res.data.success) {
        this.isLoading = false
        return true
      }

      return false
    },
  },
}
</script>
