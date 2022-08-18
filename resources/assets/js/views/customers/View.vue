<template>
  <base-page>
    <div class="page-header">
      <div class="row align-items-center">
          <div class="col"></div>
          <div class="col-auto">
            <router-link  :to="`/admin/customers/${$route.params.id}/edit`" class="btn sent_button mr-1">{{ $t('general.edit') }}</router-link>
            <button type="button" class="btn btn-primary" data-toggle="dropdown"> {{ $t('customers.new_transaction') }}</button>
            <div class="dropdown-menu">
                <router-link class="dropdown-item"  :to="`/admin/estimates/create?customer=${$route.params.id}`" > {{ $t('estimates.new_estimate') }}</router-link>
                <router-link class="dropdown-item" :to="`/admin/invoices/create?customer=${$route.params.id}`">{{ $t('invoices.new_invoice') }}</router-link>
                <router-link class="dropdown-item" :to="`/admin/payments/create?customer=${$route.params.id}`">{{ $t('payments.new_payment') }}</router-link>
                <router-link class="dropdown-item" :to="`/admin/expenses/create?customer=${$route.params.id}`">{{ $t('expenses.new_expense') }}</router-link>
            </div>
          </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12 d-flex">
          <div class="card flex-fill">
            <CustomerChart />
            <CustomerInfo />
          </div>
      </div>
    </div>
  </base-page>
</template>
<script>
import {
  DotsHorizontalIcon,
  TrashIcon,
  DocumentIcon,
  DocumentTextIcon,
  CreditCardIcon,
  CalculatorIcon,
} from '@vue-hero-icons/solid'
import LineChart from '../../components/chartjs/LineChart'
import CustomerChart from './partials/CustomerChart'
import { mapActions, mapGetters } from 'vuex'
import CustomerInfo from './partials/CustomerInfo'

export default {
  components: {
    LineChart,
    DotsHorizontalIcon,
    DocumentIcon,
    DocumentTextIcon,
    CreditCardIcon,
    CalculatorIcon,
    CustomerChart,
    TrashIcon,
    CustomerInfo,
  },
  data() {
    return {
      customer: null,
    }
  },

  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),
    pageTitle() {
      return this.selectedViewCustomer.customer
        ? this.selectedViewCustomer.customer.name
        : ''
    },
  },
  created() {
    this.fetchViewCustomer({ id: this.$route.params.id })
  },
  methods: {
    ...mapActions('customer', [
      'fetchViewCustomer',
      'selectCustomer',
      'deleteMultipleCustomers',
    ]),

    async removeCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
      
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let data = [id]
          this.selectCustomer(data)
          let res = await this.deleteMultipleCustomers()
          if (res.data.success) {
            window.toastr['success'](this.$tc('customers.deleted_message'))
            this.$router.push('/admin/customers')
            return true
          } else if (request.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
  },
}
</script>
