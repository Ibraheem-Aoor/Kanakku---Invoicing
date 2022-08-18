<template>
  <div class="card card-body" v-if="!isLoading">
    <form action="" class="mt-6" @submit.prevent="updatePaymentSetting">
      <div class="form-group">
          <label>{{$t('settings.customization.payments.payment_prefix')}}</label>
          <input type="text" class="form-control" v-model="payments.payment_prefix"
          :class="{'is-invalid': $v.payments.payment_prefix.$error}"
          style="max-width: 30%"
          @input="$v.payments.payment_prefix.$touch()"
          @keyup="changeToUppercase('PAYMENTS')">
      </div>
       <div class="form-group">
         <label>{{$t('settings.customization.payments.default_payment_email_body')}}</label>
          <vue-editor v-model="payments.payment_mail_body" />
          <base-custom-input
          v-model="payments.payment_mail_body"
          :fields="mailFields"
          class="mt-2"
        />
       </div>
        <div class="form-group">
         <label>{{$t('settings.customization.payments.company_address_format')}}</label>
          <vue-editor v-model="payments.company_address_format" />
          <base-custom-input
          v-model="payments.company_address_format"
          :fields="companyFields"
          class="mt-2"
        />
       </div>
         <div class="form-group">
         <label>{{$t('settings.customization.payments.from_customer_address_format')}}</label>
          <vue-editor v-model="payments.from_customer_address_format" />
          <base-custom-input
          v-model="payments.from_customer_address_format"
          :fields="customerAddressFields"
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
      paymentAutogenerate: false,

      payments: {
        payment_prefix: null,
        payment_mail_body: null,
        from_customer_address_format: null,
        company_address_format: null,
      },

      mailFields: [
        'customer',
        'customerCustom',
        'company',
        'payment',
        'paymentCustom',
      ],
      customerAddressFields: [
        'billing',
        'customer',
        'customerCustom',
        'paymentCustom',
      ],
      companyFields: ['company', 'paymentCustom'],
      isLoading: false,
    }
  },
  computed: {
    paymentPrefixError() {
      if (!this.$v.payments.payment_prefix.$error) {
        return ''
      }

      if (!this.$v.payments.payment_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.payments.payment_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.payments.payment_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
  },

  validations: {
    payments: {
      payment_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
  },

  watch: {
    settings(val) {
      this.payments.payment_prefix = val ? val.payment_prefix : ''

      this.payments.payment_mail_body = val ? val.payment_mail_body : ''

      this.payments.company_address_format = val
        ? val.payment_company_address_format
        : ''

      this.payments.from_customer_address_format = val
        ? val.payment_from_customer_address_format
        : ''

      this.payment_auto_generate = val ? val.payment_auto_generate : ''

      if (this.payment_auto_generate === 'YES') {
        this.paymentAutogenerate = true
      } else {
        this.paymentAutogenerate = false
      }
    },
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('company', ['updateCompanySettings']),

    changeToUppercase(currentTab) {
      if (currentTab === 'PAYMENTS') {
        this.payments.payment_prefix = this.payments.payment_prefix.toUpperCase()
        return true
      }
    },

    async setPaymentSetting() {
      let data = {
        settings: {
          payment_auto_generate: this.paymentAutogenerate ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async updatePaymentSetting() {
      this.$v.payments.$touch()

      if (this.$v.payments.$invalid) {
        return false
      }

      let data = {
        settings: {
          payment_prefix: this.payments.payment_prefix,
          payment_mail_body: this.payments.payment_mail_body,
          payment_company_address_format: this.payments.company_address_format,
          payment_from_customer_address_format: this.payments
            .from_customer_address_format,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.customization.payments.payment_setting_updated')
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
