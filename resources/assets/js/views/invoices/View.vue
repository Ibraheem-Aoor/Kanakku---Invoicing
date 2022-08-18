<template>
  <base-page v-if="invoice" class="xl:pl-96">
   <div class="row justify-content-center">
            <div class="col-xl-10">

              
          <div class="page-header">
            <div class="row align-items-center">
              <div class="col">
                <h3 class="page-title">{{invoice.invoice_number}}</h3>
              </div>
              <div class="col-auto">
                <a v-if="invoice.status === 'DRAFT'"  @click="onMarkAsSent" class="btn sent_button mr-1">{{ $t('invoices.mark_as_sent') }}
                </a>
                <a v-if="invoice.status === 'DRAFT'" @click="onSendInvoice($route.params.id, invoice)" class="btn btn-primary mr-1">{{ $t('invoices.send_invoice') }}
                </a>
                <router-link  :to="`/admin/payments/${$route.params.id}/create`" v-if="invoice.status === 'SENT' || invoice.status === 'OVERDUE' || invoice.status === 'VIEWED' " class="btn btn-primary mr-1"> {{ $t('payments.record_payment') }}
                </router-link>      
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
                      <div class="col-md-6">
                        <p class="invoice-details">
                          <strong>{{$t('invoices.invoice_number')}}</strong> {{invoice.invoice_number}} <br>
                          <strong>{{$t('invoices.invoice_date')}}</strong> {{invoice.formattedInvoiceDate}} <br>
                          <strong>{{$t('invoices.due_date')}}</strong> {{invoice.formattedDueDate}}
                        </p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Invoice Item -->
                  <div class="invoice-item">
                    <div class="row">
                      <div class="col-md-6">
                      </div>
                      <div class="col-md-6">
                        <div class="invoice-info invoice-info2">
                          <strong class="customer-text">Invoice To</strong>
                          <p class="invoice-details">
                            {{invoice.user && invoice.user.name}}<br>
                            {{invoice.user && invoice.user.phone}} <br>
                            {{invoice.user && invoice.user.email}} <br>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /Invoice Item -->
                  
               
                  <!-- Invoice Item -->
                  <div class="invoice-item invoice-table-wrap">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="invoice-table table table-bordered">
                            <thead>
                              <tr>
                                <th>{{ $tc('items.item', 2) }}</th>
                                <th class="text-center">{{ $t('invoices.item.quantity') }}</th>
                                <th class="text-center">{{ $t('invoices.item.price') }}</th>
                                <th class="text-center" v-if="invoice.discount_per_item==='YES'">{{ $tc('estimates.item.discount') }}</th>
                                <th class="text-right">{{ $t('invoices.item.total') }}</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in invoice.items" :key="item.id">
                                <td>{{item.name}}</td>
                                <td class="text-center">{{item.quantity}}</td>
                                <td class="text-center"><div v-html="$utils.formatMoney(item.price, invoice.user.currency)" /></td>
                                <td class="text-center" v-if="item.discount_type === 'percentage' && invoice.discount_per_item==='YES'">{{item.discount+'%'}}</td>
                                <td class="text-center" v-else-if="item.discount_type === 'fixed' && invoice.discount_per_item==='YES'"><div v-html="$utils.formatMoney(item.discount_val,invoice.user.currency)" /></td>
                                <td class="text-right"><div v-html="$utils.formatMoney(item.total, invoice.user.currency)" /></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-md-6 col-xl-4 ml-auto">
                        <div class="table-responsive">
                          <table class="invoice-table-two table">
                            <tbody>
                            <tr>
                              <th>{{ $t('invoices.sub_total') }}</th>
                              <td><div v-html="$utils.formatMoney(invoice.sub_total, invoice.user.currency)" /></td>
                            </tr>
                             <tr v-if="invoice.discount">
                              <th>{{ $t('invoices.discount') }}{{invoice.discount_type === 'percentage' ? (invoice.discount+'%') : '' }}:</th>
                              <td><span><div v-html="$utils.formatMoney(invoice.discount_val,invoice.user.currency)" /></span></td>
                            </tr>
                            <tr v-for="(item,index) in taxes" :key="index">
                              <th>{{index === 0 ? $t('estimates.tax') : ''}}</th>
                              <td class="float-right"><span>
                                {{item.name}} ({{item.percent}}%) </span> <span> <div v-html="$utils.formatMoney(item.amount,invoice.user.currency)" /></span>
                              </td>
                            </tr>
                            <tr>
                              <th>{{$t('invoices.total')}}</th>
                              <td><span><div v-html="$utils.formatMoney(invoice.total, invoice.user.currency)" /></span></td>
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
            <SendInvoiceModal ref="modal"/>
          </div>
  </base-page>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
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
import SendInvoiceModal from '../../components/base/modal/SendInvoiceModal'


const _ = require('lodash')
export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    LinkIcon,
    PencilIcon,
    TrashIcon,
    SendInvoiceModal,
  },
  data() {
    return {
      id: null,
      count: null,
      invoices: [],
      invoice: null,
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
      taxes: [],
    }
  },
  computed: {
    pageTitle() {
      return this.invoice.invoice_number
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
      return `/invoices/pdf/${this.invoice.unique_hash}`
    },
    getCurrentInvoiceId() {
      if (this.invoice && this.invoice.id) {
        return this.invoice.id
      }
      return null
    },
  },
  watch: {
    $route(to, from) {
      this.loadInvoice()
    },
  },
  created() {
    this.loadInvoices()
    this.loadInvoice()
    this.onSearch = _.debounce(this.onSearch, 500)
  },
  methods: {
    ...mapActions('invoice', [
      'fetchInvoices',
      'getRecord',
      'searchInvoice',
      'markAsSent',
      'sendEmail',
      'deleteInvoice',
      'selectInvoice',
      'fetchInvoice',
    ]),

    ...mapActions('modal', ['openModal']),

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },
    getTaxes(items) {
          this.taxes = []
          if(this.invoice.tax_per_item === 'YES') {
               items.forEach((item) => {
                item.taxes.forEach((tax) => {
                  let found = this.taxes.find((_tax) => {
                    return _tax.tax_type_id === tax.tax_type_id
                  })

                  if (found) {
                    found.amount += tax.amount
                  } else if (tax.tax_type_id) {
                    this.taxes.push({
                      tax_type_id: tax.tax_type_id,
                      amount: tax.amount,
                      percent: tax.percent,
                      name: tax.name,
                    })
                  }
                })
              })
          } else {
            this.taxes = this.invoice.taxes
          }
    },  
    async loadInvoices() {
      let response = await this.fetchInvoices({ limit: 'all' })
      if (response.data) {
        this.invoices = response.data.invoices.data
      }
      setTimeout(() => {
        this.scrollToInvoice()
      }, 500)
    },
    scrollToInvoice() {
      const el = document.getElementById(`invoice-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadInvoice() {
      let response = await this.fetchInvoice(this.$route.params.id)

      if (response.data) {
        this.invoice = response.data.invoice
        this.getTaxes(response.data.invoice && response.data.invoice.items || [])

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
      let response = await this.searchInvoice(data)
      this.isSearching = false
      if (response.data) {
        this.invoices = response.data.invoices.data
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
    async onMarkAsSent() {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('invoices.invoice_mark_as_sent'),
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            this.isMarkingAsSent = true
            let response = await this.markAsSent({
              id: this.invoice.id,
              status: 'SENT',
            })
            this.isMarkingAsSent = false
            if (response.data) {
              this.invoice.status = 'SENT'
              window.toastr['success'](
                this.$tc('invoices.marked_as_sent_message')
              )
            }
          }
        })
    },
    async onSendInvoice(id,data) {
      this.$refs.modal.show(id,data);
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/invoices/pdf/${this.invoice.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$t('general.copied_pdf_url_clipboard'))
    },
    async removeInvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this invoice!',
        
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deleteInvoice({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$tc('invoices.deleted_message', 1))
              this.$router.push('/admin/invoices')
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    },
  },
}
</script>
