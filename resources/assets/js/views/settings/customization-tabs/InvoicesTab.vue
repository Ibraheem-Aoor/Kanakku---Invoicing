<template>
  <div class="card card-body">
    <form action="" class="mt-6" @submit.prevent="updateInvoiceSetting">
      <div class="form-group">
          <label>{{$t('settings.customization.invoices.invoice_prefix')}}</label>
          <input type="text" class="form-control" v-model="invoices.invoice_prefix"
          :invalid="$v.invoices.invoice_prefix.$error"
          style="max-width: 30%"
          @input="$v.invoices.invoice_prefix.$touch()"
          @keyup="changeToUppercase('INVOICES')">
      </div>
       <div class="form-group">
         <label>{{$t('settings.customization.invoices.default_invoice_email_body')}}</label>
          <vue-editor v-model="invoices.invoice_mail_body" />
          <base-custom-input
          v-model="invoices.invoice_mail_body"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
       </div>
        <div class="form-group">
         <label>{{$t('settings.customization.invoices.company_address_format')}}</label>
          <vue-editor v-model="invoices.company_address_format" />
          <base-custom-input
          v-model="invoices.company_address_format"
          :fields="companyFields"
          class="mt-2"
        />
       </div>
         <div class="form-group">
         <label>{{$t('settings.customization.invoices.shipping_address_format')}}</label>
          <vue-editor v-model="invoices.shipping_address_format" />
          <base-custom-input
          v-model="invoices.shipping_address_format"
          :fields="shippingFields"
          class="mt-2"
        />
       </div>
         <div class="form-group">
         <label>{{$t('settings.customization.invoices.billing_address_format')}}</label>
          <vue-editor v-model="invoices.billing_address_format" />
          <base-custom-input
          v-model="invoices.billing_address_format"
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
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import { mapActions, mapGetters } from 'vuex'
import { VueEditor } from "vue2-editor";

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
      invoiceAutogenerate: false,

      invoices: {
        invoice_prefix: null,
        invoice_mail_body: null,
        company_address_format: null,
        shipping_address_format: null,
        billing_address_format: null,
      },
      isLoading: false,
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'invoice',
        'invoiceCustom',
        'company',
      ],
      billingFields: ['billing', 'customer', 'customerCustom', 'invoiceCustom'],
      shippingFields: [
        'shipping',
        'customer',
        'customerCustom',
        'invoiceCustom',
      ],
      companyFields: ['company', 'invoiceCustom'],
    }
  },

  computed: {
    invoicePrefixError() {
      if (!this.$v.invoices.invoice_prefix.$error) {
        return ''
      }

      if (!this.$v.invoices.invoice_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.invoices.invoice_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.invoices.invoice_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
  },

  watch: {
    settings(val) {
      this.invoices.invoice_prefix = val ? val.invoice_prefix : ''

      this.invoices.invoice_mail_body = val ? val.invoice_mail_body : null
      this.invoices.company_address_format = val
        ? val.invoice_company_address_format
        : null
      this.invoices.shipping_address_format = val
        ? val.invoice_shipping_address_format
        : null
      this.invoices.billing_address_format = val
        ? val.invoice_billing_address_format
        : null

      this.invoice_auto_generate = val ? val.invoice_auto_generate : ''

      if (this.invoice_auto_generate === 'YES') {
        this.invoiceAutogenerate = true
      } else {
        this.invoiceAutogenerate = false
      }
    },
  },

  validations: {
    invoices: {
      invoice_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),

    async setInvoiceSetting() {
      let data = {
        settings: {
          invoice_auto_generate: this.invoiceAutogenerate ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    changeToUppercase(currentTab) {
      if (currentTab === 'INVOICES') {
        this.invoices.invoice_prefix = this.invoices.invoice_prefix.toUpperCase()

        return true
      }
    },

    async updateInvoiceSetting() {
      this.$v.invoices.$touch()

      if (this.$v.invoices.$invalid) {
        return false
      }

      let data = {
        settings: {
          invoice_prefix: this.invoices.invoice_prefix,
          invoice_mail_body: this.invoices.invoice_mail_body,
          invoice_company_address_format: this.invoices.company_address_format,
          invoice_billing_address_format: this.invoices.billing_address_format,
          invoice_shipping_address_format: this.invoices
            .shipping_address_format,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.customization.invoices.invoice_setting_updated')
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
