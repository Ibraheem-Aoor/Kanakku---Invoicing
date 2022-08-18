<template>
<div>
  <div class="dropdown">
  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
    {{ $t('settings.customization.addresses.insert_fields') }}
  </button>
  <div class="dropdown-menu p-3 double_icon">
     <div style="display: flex">
          <ul v-for="(type, index) in fieldList" :key="index" class="list-none p-2">
            <li class="text-grey">
              {{ type.label }}
            </li>
            <li
              v-for="(field, index) in type.fields"
              :key="index"
              
              @click="insertField(field.value)"
            >
              <div class="flex">
                <chevron-double-right-icon />{{
                  field.label
                }}
              </div>
            </li>
          </ul>
        </div>
  </div>
</div>
</div>
</template>

<script>
import { PlusSmIcon } from '@vue-hero-icons/outline'
import { ChevronDoubleRightIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    PlusSmIcon,
    ChevronDoubleRightIcon,
  },
  props: {
    value: {
      type: [String, Number, File],
      default: '',
    },
    types: {
      type: Array,
      default: null,
    },
    placeholder: {
      type: String,
      default: '',
    },
    rows: {
      type: String,
      default: '10',
    },
    cols: {
      type: String,
      default: '30',
    },
    invalid: {
      type: Boolean,
      default: false,
    },
    fields: {
      type: Array,
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      fieldList: [],
      invoiceFields: [],
      estimateFields: [],
      paymentFields: [],
      customerFields: [],
      position: null,
      inputValue: this.value,
    }
  },
  computed: {
    ...mapGetters('customFields', ['getCustomFields']),
    isFieldValid() {
      return this.invalid
    },
  },
  watch: {
    value() {
      this.inputValue = this.value
    },
    fields() {
      if (this.fields && this.fields.length > 0) {
        this.getFields()
      }
    },
    getCustomFields(newValue) {
      this.invoiceFields = newValue
        ? newValue.filter((field) => field.model_type === 'Invoice')
        : []
      this.customerFields = newValue
        ? newValue.filter((field) => field.model_type === 'Customer')
        : []
      this.paymentFields = newValue
        ? newValue.filter((field) => field.model_type === 'Payment')
        : []
      this.estimateFields = newValue.filter(
        (field) => field.model_type === 'Estimate'
      )
      this.getFields()
    },
  },
  async mounted() {
    this.getFields()
    await this.fetchNoteCustomFields({ limit: 'all' })
  },
  methods: {
    ...mapActions('customFields', ['fetchNoteCustomFields']),

    getFields() {
      this.fieldList = []

      if (this.fields && this.fields.length > 0) {
        if (this.fields.find((field) => field == 'shipping')) {
          this.fieldList.push({
            label: 'Shipping Address',
            fields: [
              { label: 'Address name', value: 'SHIPPING_ADDRESS_NAME' },
              { label: 'Country', value: 'SHIPPING_COUNTRY' },
              { label: 'State', value: 'SHIPPING_STATE' },
              { label: 'City', value: 'SHIPPING_CITY' },
              { label: 'Address Street 1', value: 'SHIPPING_ADDRESS_STREET_1' },
              { label: 'Address Street 2', value: 'SHIPPING_ADDRESS_STREET_2' },
              { label: 'Phone', value: 'SHIPPING_PHONE' },
              { label: 'Zip Code', value: 'SHIPPING_ZIP_CODE' },
            ],
          })
        }

        if (this.fields.find((field) => field == 'billing')) {
          this.fieldList.push({
            label: 'Billing Address',
            fields: [
              { label: 'Address name', value: 'BILLING_ADDRESS_NAME' },
              { label: 'Country', value: 'BILLING_COUNTRY' },
              { label: 'State', value: 'BILLING_STATE' },
              { label: 'City', value: 'BILLING_CITY' },
              { label: 'Address Street 1', value: 'BILLING_ADDRESS_STREET_1' },
              { label: 'Address Street 2', value: 'BILLING_ADDRESS_STREET_2' },
              { label: 'Phone', value: 'BILLING_PHONE' },
              { label: 'Zip Code', value: 'BILLING_ZIP_CODE' },
            ],
          })
        }

        if (this.fields.find((field) => field == 'customer')) {
          this.fieldList.push({
            label: 'Customer',
            fields: [
              { label: 'Display Name', value: 'CONTACT_DISPLAY_NAME' },
              { label: 'Contact Name', value: 'PRIMARY_CONTACT_NAME' },
              { label: 'Email', value: 'CONTACT_EMAIL' },
              { label: 'Phone', value: 'CONTACT_PHONE' },
              { label: 'Website', value: 'CONTACT_WEBSITE' },
              ...this.customerFields.map((i) => ({
                label: i.label,
                value: i.slug,
              })),
            ],
          })
        }

        if (this.fields.find((field) => field == 'invoice')) {
          this.fieldList.push({
            label: 'Invoice',
            fields: [
              { label: 'Date', value: 'INVOICE_DATE' },
              { label: 'Due Date', value: 'INVOICE_DUE_DATE' },
              { label: 'Number', value: 'INVOICE_NUMBER' },
              { label: 'Ref Number', value: 'INVOICE_REF_NUMBER' },
              { label: 'Invoice Link', value: 'INVOICE_LINK' },
              ...this.invoiceFields.map((i) => ({
                label: i.label,
                value: i.slug,
              })),
            ],
          })
        }

        if (this.fields.find((field) => field == 'estimate')) {
          this.fieldList.push({
            label: 'Estimate',
            fields: [
              { label: 'Date', value: 'ESTIMATE_DATE' },
              { label: 'Expiry Date', value: 'ESTIMATE_EXPIRY_DATE' },
              { label: 'Number', value: 'ESTIMATE_NUMBER' },
              { label: 'Ref Number', value: 'ESTIMATE_REF_NUMBER' },
              { label: 'Estimate Link', value: 'ESTIMATE_LINK' },
              ...this.estimateFields.map((i) => ({
                label: i.label,
                value: i.slug,
              })),
            ],
          })
        }

        if (this.fields.find((field) => field == 'payment')) {
          this.fieldList.push({
            label: 'Payment',
            fields: [
              { label: 'Date', value: 'PAYMENT_DATE' },
              { label: 'Number', value: 'PAYMENT_NUMBER' },
              { label: 'Mode', value: 'PAYMENT_MODE' },
              { label: 'Amount', value: 'PAYMENT_AMOUNT' },
              { label: 'Payment Link', value: 'PAYMENT_LINK' },
              ...this.paymentFields.map((i) => ({
                label: i.label,
                value: i.slug,
              })),
            ],
          })
        }

        if (this.fields.find((field) => field == 'company')) {
          this.fieldList.push({
            label: 'Company',
            fields: [
              { label: 'Company Name', value: 'COMPANY_NAME' },
              { label: 'Country', value: 'COMPANY_COUNTRY' },
              { label: 'State', value: 'COMPANY_STATE' },
              { label: 'City', value: 'COMPANY_CITY' },
              { label: 'Address Street 1', value: 'COMPANY_ADDRESS_STREET_1' },
              { label: 'Address Street 2', value: 'COMPANY_ADDRESS_STREET_2' },
              { label: 'Phone', value: 'COMPANY_PHONE' },
              { label: 'Zip Code', value: 'COMPANY_ZIP_CODE' },
            ],
          })
        }
      }
    },

    insertField(varName) {
      if (this.inputValue) {
        this.inputValue += `{${varName}}`
      } else {
        this.inputValue = `{${varName}}`
      }
      this.$emit('input', this.inputValue)
    },

    handleInput(e) {
      this.$emit('input', this.inputValue)
    },

    handleChange(e) {
      this.$emit('change', this.inputValue)
    },

    handleKeyupEnter(e) {
      this.$emit('keyup', this.inputValue)
    },

    handleKeyDownEnter(e) {
      this.$emit('keydown', e, this.inputValue)
    },

    handleFocusOut(e) {
      this.$emit('blur', this.inputValue)
    },
  },
}
</script>
