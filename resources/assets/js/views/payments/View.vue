<template>
  <base-page v-if="payment" class="xl:pl-96">
     <div class="row justify-content-center">
            <div class="col-xl-10">


<div class="page-header">
            <div class="row align-items-center">
              <div class="col">
                <h3 class="page-title">{{payment.payment_number}}</h3>
              </div>
              <div class="col-auto">
                
                <a  @click="onPaymentSend($route.params.id, payment)" class="btn btn-primary mr-1"> {{ $t('payments.send_payment_receipt') }}
                </a>      
              </div>
            </div>
          </div>
              <div class="card">
                <div class="card-body">
                  <div class="invoice-item">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="invoice-logo">
                          <img src="assets/img/logo.png" alt="logo">
                        </div>
                      </div>
                      <div class="col-md-6 text-right">
                        <p class="invoice-details">
                          <strong>{{$t('payments.payment_number')}}</strong>{{payment.payment_number}}<br>
                          <strong>{{$t('payments.date')}}</strong>{{payment.formattedPaymentDate}}<br>
                          <!-- <strong>{{$t('payments.note')}}</strong><div v-html="payment.notes" /><br> -->
                          <strong>{{$t('invoices.title')}}</strong>{{payment.invoice && payment.invoice.invoice_number || '-' }}
                        </p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Invoice Item -->
                  <div class="invoice-item">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="invoice-info">
                         <strong class="customer-text">{{$t('payments.payment_mode')}}</strong>
                          <p class="invoice-details invoice-details-two">
                            {{payment.payment_method && payment.payment_method.name}}<br>
                          </p>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="invoice-info invoice-info2">
                          <strong class="customer-text">Invoice To</strong>
                          <p class="invoice-details">
                            {{payment.user.name}}<br>
                            {{payment.user && payment.user.email}} <br>
                            {{payment.user && payment.user.phone}} <br>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /Invoice Item -->

                  <!-- Invoice Item -->
                  <div class="invoice-item invoice-table-wrap">
                    <div class="row">
                      <div class="col-md-6 col-xl-4 ml-auto">
                        <div class="table-responsive">
                          <table class="invoice-table-two table">
                            <tbody>
                            <tr>
                              <th>{{$t('invoices.total')}}</th>
                              <td><div v-html="$utils.formatMoney(payment.amount, payment.user.currency)" /></td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /Invoice Item -->
                  
                </div>
              </div>
            </div>
            <SendPaymentModal ref="modal"/>
          </div>
  </base-page>
</template>
<script>
import {
  DotsHorizontalIcon,
  FilterIcon,
  SortAscendingIcon,
  SortDescendingIcon,
  SearchIcon,
  LinkIcon,
  TrashIcon,
  PencilIcon,
} from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import SendPaymentModal from '../../components/base/modal/SendPaymentModal'
const _ = require('lodash')
export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    TrashIcon,
    PencilIcon,
    LinkIcon,
    SendPaymentModal,
  },
  data() {
    return {
      id: null,
      count: null,
      payments: [],
      payment: null,
      currency: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      isRequestOnGoing: false,
      isSearching: false,
      isSendingEmail: false,
      isMarkingAsSent: false,
    }
  },
  computed: {
    pageTitle() {
      return this.payment.payment_number
    },
    getOrderBy() {
      if (
        this.searchData.orderBy === 'asc' ||
        this.searchData.orderBy == null
      ) {
        return true
      }
      return false
    },
    getOrderName() {
      if (this.getOrderBy) {
        return this.$t('general.ascending')
      }
      return this.$t('general.descending')
    },
    shareableLink() {
      return `/payments/pdf/${this.payment.unique_hash}`
    },
  },

  watch: {
    $route(to, from) {
      this.loadPayment()
    },
  },

  created() {
    this.loadPayments()
    this.loadPayment()
    this.onSearch = _.debounce(this.onSearch, 500)
  },

  methods: {
    ...mapActions('payment', [
      'fetchPayments',
      'fetchPayment',
      'sendEmail',
      'deletePayment',
      'searchPayment',
    ]),
    ...mapActions('modal', ['openModal']),

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadPayments() {
      let response = await this.fetchPayments({ limit: 'all' })
      if (response.data) {
        this.payments = response.data.payments.data
      }
      setTimeout(() => {
        this.scrollToPayment()
      }, 500)
    },
    scrollToPayment() {
      const el = document.getElementById(`payment-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadPayment() {
      let response = await this.fetchPayment(this.$route.params.id)

      if (response.data) {
        this.payment = response.data.payment
      }
    },
    async onSearch() {
      let data = ''
      if (
        this.searchData.searchText !== '' &&
        this.searchData.searchText !== null &&
        this.searchData.searchText !== undefined
      ) {
        data += `search=${this.searchData.searchText}&`
      }

      if (
        this.searchData.orderBy !== null &&
        this.searchData.orderBy !== undefined
      ) {
        data += `orderBy=${this.searchData.orderBy}&`
      }

      if (
        this.searchData.orderByField !== null &&
        this.searchData.orderByField !== undefined
      ) {
        data += `orderByField=${this.searchData.orderByField}`
      }
      this.isSearching = true
      let response = await this.searchPayment(data)
      this.isSearching = false
      if (response.data) {
        this.payments = response.data.payments.data
      }
    },
    sortData() {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearch()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearch()
      return true
    },
    async onPaymentSend(id,data) {
      this.$refs.modal.show(id, data)
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/payments/pdf/${this.payment.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$t('general.copied_pdf_url_clipboard'))
    },
    async removePayment(id) {
      this.id = id
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this payment!',
         
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deletePayment({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$tc('payments.deleted_message', 1))
              this.$router.push('/admin/payments')
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    },
  },
}
</script>
