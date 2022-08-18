<template>
  <base-page v-if="estimate" class="xl:pl-96">
     <div class="row justify-content-center">
            <div class="col-xl-10">


<div class="page-header">
            <div class="row align-items-center">
              <div class="col">
                <h3 class="page-title">{{pageTitle && pageTitle}}</h3>
              </div>
              <div class="col-auto">
                <a  v-if="estimate.status === 'DRAFT'" @click="onMarkAsSent" class="btn sent_button mr-1"> {{ $t('estimates.mark_as_sent') }}
                </a>
                <a v-if="estimate.status === 'DRAFT'" @click="onSendEstimate($route.params.id,estimate)" class="btn btn-primary mr-1">{{ $t('estimates.send_estimate') }}
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
                      <div class="col-md-6">
                        <p class="invoice-details">
                          <strong>{{$t('estimates.estimate_number')}}</strong>{{pageTitle && pageTitle}}<br>
                          <strong>{{$t('reports.estimates.estimate_date')}}</strong> {{estimate.formattedEstimateDate}}<br>
                          <strong>{{$t('estimates.due_date')}}</strong> {{estimate.formattedExpiryDate}}
                        </p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Invoice Item -->
                  <div class="invoice-item">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="invoice-info">
                          <!-- <strong class="customer-text">Invoice From</strong>
                          <p class="invoice-details invoice-details-two">
                            Darren Elder <br>
                            806  Twin Willow Lane, Old Forge,<br>
                            Newyork, USA <br>
                          </p> -->
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="invoice-info invoice-info2">
                          <strong class="customer-text">Invoice To</strong>
                          <p class="invoice-details">
                            {{estimate.user && estimate.user.name}}<br>
                            {{estimate.user && estimate.user.email}} <br>
                            {{estimate.user && estimate.user.phone}}<br>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /Invoice Item -->
                  
                  <!-- Invoice Item -->
                  <!-- <div class="invoice-item">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="invoice-info">
                          <strong class="customer-text">Payment Method</strong>
                          <p class="invoice-details invoice-details-two">
                            Debit Card <br>
                            XXXXXXXXXXXX-2541 <br>
                            HDFC Bank<br>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div> -->
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
                                <th class="text-center">{{ $tc('estimates.item.quantity') }}</th>
                                <th class="text-center">{{ $tc('estimates.item.price') }}</th>
                                <th class="text-center" v-if="estimate.discount_per_item==='YES'">{{ $tc('estimates.item.discount') }}</th>
                                <th class="text-right">{{ $tc('estimates.item.amount') }}</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in estimate.items" :key="item.id">
                                <td>{{item.name}}</td>
                                <td class="text-center">{{item.quantity}}</td>
                                <td class="text-center"><div v-html="$utils.formatMoney(item.price,estimate.user.currency)" /></td>
                                <td class="text-center" v-if="item.discount_type === 'percentage' && estimate.discount_per_item==='YES'">{{item.discount+'%'}}</td>
                                <td class="text-center" v-else-if="item.discount_type === 'fixed' && estimate.discount_per_item==='YES'"><div v-html="$utils.formatMoney(item.discount_val,estimate.user.currency)" /></td>
                                <td class="text-right"><div v-html="$utils.formatMoney(item.total,estimate.user.currency)" /></td>
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
                              <th>{{ $t('estimates.sub_total') }}</th>
                              <td><span><div v-html="$utils.formatMoney(estimate.sub_total,estimate.user.currency)" /></span></td>
                            </tr>
                            <tr v-if="estimate.discount">
                              <th>{{ $t('estimates.discount') }} {{estimate.discount_type === 'percentage' ? '('+estimate.discount+'%)' : '' }}:</th>
                              <td><span><div v-html="$utils.formatMoney(estimate.discount_val,estimate.user.currency)" /></span></td>
                            </tr>
                            <tr v-for="(item,index) in taxes" :key="index">
                              <th>{{index === 0 ? $t('estimates.tax')  : ''}}</th>
                              <td class="float-right"><span>
                                {{item.name}} ({{item.percent}}%) </span> <span> <div v-html="$utils.formatMoney(item.amount,estimate.user.currency)" /></span>
                              </td>
                            </tr>
                            <tr>
                              <th>{{ $t('estimates.total') }}</th>
                              <td><span><div v-html="$utils.formatMoney(estimate.total,estimate.user.currency)" /></span></td>
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
            <SendEstimateModal  ref="modal"/> 
          </div>
  </base-page>
</template>

<script>
import { mapActions } from 'vuex'
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
const _ = require('lodash')
import SendEstimateModal from '../../components/base/modal/SendEstimateModal'

export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    LinkIcon,
    TrashIcon,
    PencilIcon,
    SendEstimateModal,
  },
  data() {
    return {
      id: null,
      count: null,
      estimates: [],
      estimate: null,
      currency: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      status: ['DRAFT', 'SENT', 'VIEWED', 'EXPIRED', 'ACCEPTED', 'REJECTED'],
      isMarkAsSent: false,
      isSendingEmail: false,
      isRequestOnGoing: false,
      isSearching: false,
      taxes: [],
    }
  },
  computed: {
    pageTitle() {
      return this.estimate.estimate_number
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
      return `/estimates/pdf/${this.estimate.unique_hash}`
    },
    getCurrentEstimateId() {
      if (this.estimate && this.estimate.id) {
        return this.estimate.id
      }
      return null
    },
  },
  watch: {
    $route(to, from) {
      this.loadEstimate()
    },
  },
  created() {
    this.loadEstimates()
    this.loadEstimate()
    this.onSearched = _.debounce(this.onSearched, 500)
  },
  methods: {
    ...mapActions('estimate', [
      'fetchEstimates',
      'getRecord',
      'searchEstimate',
      'markAsSent',
      'sendEmail',
      'deleteEstimate',
      'selectEstimate',
      'fetchViewEstimate',
    ]),

    ...mapActions('modal', ['openModal']),

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadEstimates() {
      let response = await this.fetchEstimates({ limit: 'all' })
      if (response.data) {
        this.estimates = response.data.estimates.data
      }
      setTimeout(() => {
        this.scrollToEstimate()
      }, 500)
    },
    scrollToEstimate() {
      const el = document.getElementById(`estimate-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    getTaxes(items) {
          this.taxes = []
          if(this.estimate.tax_per_item === 'YES') {
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
            this.taxes = this.estimate.taxes
          }
    },
    async loadEstimate() {
      let response = await this.fetchViewEstimate(this.$route.params.id)
      
      if (response.data) {
        this.estimate = { ...response.data.estimate }
        this.getTaxes(response.data.estimate && response.data.estimate.items || []);
      }
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/estimates/pdf/${this.estimate.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$tc('general.copied_pdf_url_clipboard'))
    },
    async onSearched() {
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
      let response = await this.searchEstimate(data)
      this.isSearching = false
      if (response.data) {
        this.estimates = response.data.estimates.data
      }
    },
    sortData() {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearched()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearched()
      return true
    },
    async onMarkAsSent() {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_mark_as_sent'),
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            this.isMarkAsSent = true
            let response = await this.markAsSent({
              id: this.estimate.id,
              status: 'SENT',
            })
            this.isMarkAsSent = false
            if (response.data) {
              this.estimate.status = 'SENT'
              window.toastr['success'](
                this.$tc('estimates.mark_as_sent_successfully')
              )
            }
          }
        })
    },
    async onSendEstimate(id, estimate) {
     this.$refs.modal.show(id, estimate)
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/estimates/pdf/${this.estimate.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$tc('general.copied_pdf_url_clipboard'))
    },
    async removeEstimate(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this estimate!',
         
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deleteEstimate({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$tc('estimates.deleted_message', 1))
              this.$router.push('/admin/estimates')
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    },
  },
}
</script>
