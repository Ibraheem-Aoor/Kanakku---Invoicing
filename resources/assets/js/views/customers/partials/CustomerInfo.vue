<template>
    <div class="card-body">
      <h5 class="basic_info mt-4">{{ $t('customers.basic_info') }}</h5> 
      <div class="row">
        <div class="col-md-3">
          <span>{{ $t('customers.display_name') }}</span>
          <p class="h6 text-black">{{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.name
                ? selectedViewCustomer.customer.name
                : ''
            }}</p>
        </div>
        <div class="col-md-3">
          <span>{{ $t('customers.phone_number') }}</span>
          <p class="h6 text-black"> {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.phone
                ? selectedViewCustomer.customer.phone
                : ''
            }}</p>
        </div>
        <div class="col-md-3">
          <span>{{ $t('customers.email') }}</span>
          <p class="h6 text-black"> {{
              selectedViewCustomer.customer &&
              selectedViewCustomer.customer.email
                ? selectedViewCustomer.customer.email
                : ''
            }}</p>
        </div>
      </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  data() {
    return {
      customer: null,
      customFields: [],
    }
  },
  computed: {
    getFormattedBillingAddress() {
      let billingAddress = ``

      if (!this.selectedViewCustomer.customer) {
        return billingAddress
      }

      if (!this.selectedViewCustomer.customer.billing_address) {
        return billingAddress
      }

      if (this.selectedViewCustomer.customer.billing_address.address_street_1) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.address_street_1},</span><br>`
      }
      if (this.selectedViewCustomer.customer.billing_address.address_street_2) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.address_street_2},</span><br>`
      }
      if (this.selectedViewCustomer.customer.billing_address.city) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.city},</span> `
      }
      if (this.selectedViewCustomer.customer.billing_address.state) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.state},</span><br>`
      }
      if (this.selectedViewCustomer.customer.billing_address.country) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.country.name}.</span> `
      }
      if (this.selectedViewCustomer.customer.billing_address.zip) {
        billingAddress += `<span>${this.selectedViewCustomer.customer.billing_address.zip}.</span> `
      }
      return billingAddress
    },
    getFormattedShippingAddress() {
      let shippingAddress = ``

      if (!this.selectedViewCustomer.customer) {
        return shippingAddress
      }

      if (!this.selectedViewCustomer.customer.shipping_address) {
        return shippingAddress
      }

      if (
        this.selectedViewCustomer.customer.shipping_address.address_street_1
      ) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.address_street_1},</span><br>`
      }
      if (
        this.selectedViewCustomer.customer.shipping_address.address_street_2
      ) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.address_street_2},</span><br>`
      }
      if (this.selectedViewCustomer.customer.shipping_address.city) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.city},</span> `
      }
      if (this.selectedViewCustomer.customer.shipping_address.state) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.state},</span><br>`
      }
      if (this.selectedViewCustomer.customer.shipping_address.country) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.country.name}.</span> `
      }
      if (this.selectedViewCustomer.customer.shipping_address.zip) {
        shippingAddress += `<span>${this.selectedViewCustomer.customer.shipping_address.zip}.</span> `
      }
      return shippingAddress
    },

    getCustomField() {
      if (this.selectedViewCustomer.customer.fields) {
        return this.selectedViewCustomer.customer.fields
      }
      return []
    },

    ...mapGetters('customer', ['selectedViewCustomer']),
  },
  watch: {
    $route(to, from) {
      this.customer = this.selectedViewCustomer
    },
  },
}
</script>
