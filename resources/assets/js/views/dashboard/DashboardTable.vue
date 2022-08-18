<template>
    <div class="row">
          <base-loader v-if="isRequestOngoing" :show-bg-overlay="true" />
						<div class="col-md-6 col-sm-6">
							<div class="card">
								<div class="card-header">
									<div class="row">
										<div class="col">
											<h5 class="card-title">{{ $t('dashboard.recent_invoices_card.title') }}</h5>
										</div>
										<div class="col-auto">
											<router-link to="/admin/invoices" class="btn-right btn btn-sm btn-outline-primary">
												{{ $t('dashboard.recent_invoices_card.view_all') }}
											</router-link>
										</div>
									</div>
								</div>
                
								<div class="card-body">
									<div class="mb-3">
										<div class="progress progress-md rounded-pill mb-3">
											<div class="progress-bar bg-success" role="progressbar"  v-bind:style="{ width: invoiceData.paidCount+'%' }" :aria-valuenow="invoiceData.paidCount" aria-valuemin="0" aria-valuemax="100"></div>
											<div class="progress-bar bg-warning" role="progressbar" v-bind:style="{ width: invoiceData.unPaidCount+'%' }" :aria-valuenow="invoiceData.unPaidCount" aria-valuemin="0" aria-valuemax="100"></div>
											<div class="progress-bar bg-danger" role="progressbar" v-bind:style="{ width: invoiceData.partiallyPaidCount+'%' }" :aria-valuenow="invoiceData.partiallyPaidCount" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<div class="row">
											<div class="col-auto">
												<i class="fas fa-circle text-success mr-1"></i> Paid
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-warning mr-1"></i> Unpaid
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-danger mr-1"></i> Partially
											</div>
										</div>
									</div>
				
									<div class="table-responsive">
									
										<table class="table table-stripped table-hover">
											<thead class="thead-light">
												<tr>
												   <th>{{ $t('dashboard.recent_invoices_card.customer') }}</th>
												   <th>{{ $t('dashboard.recent_invoices_card.amount_due') }}</th>
												   <th>{{ $t('dashboard.recent_invoices_card.due_on') }}</th>
												   <th>{{ $t('invoices.status') }}</th>
												   <th class="text-right">Action</th>
												</tr>
											</thead>
											<tbody v-if="getDueInvoices.length > 0">
												<tr v-for="row in getDueInvoices" :key="row.id" >
                         
													<td >
														<h2 class="table-avatar">
                              <router-link  :to="{ path: `customers/${row.user.id}/view` }" class="avatar avatar-sm mr-2"><img class="avatar-img rounded-circle" :src="$utils.userImage(row.user.avatar)" @error="altImg" alt="User Image"></router-link>
															 <router-link
                                :to="{ path: `invoices/${row.id}/view` }"
                                class="font-medium text-primary-500"
                              >
                              {{ row.user.name }}</router-link>
														</h2>
													</td>
													<td><div
                v-html="$utils.formatMoney(row.due_amount, row.user.currency)"
              /></td>
													<td>{{ row.formattedDueDate }}</td>
													<td><span  :style="{ 'background-color': `${$utils.getBadgeStatusColor(row.status).bgColor}`, 'color': $utils.getBadgeStatusColor(row.status).color }"> {{
                  row.status != 'PARTIALLY_PAID'
                    ? row.status
                    : row.status.replace('_', ' ')
                }}</span></td>
													<td class="text-right">
														<div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<router-link class="dropdown-item" :to="`invoices/${row.id}/edit`"><i class="far fa-edit mr-2"></i>{{ $t('general.edit') }}</router-link>
																<router-link class="dropdown-item" :to="`invoices/${row.id}/view`"><i class="far fa-eye mr-2"></i>{{ $t('invoices.view') }}</router-link>
																<a class="dropdown-item" @click="removeInvoice(row.id)"><i class="far fa-trash-alt mr-2"></i>{{ $t('general.delete') }}</a>
																<a class="dropdown-item" v-if="row.status === 'DRAFT'"  @click="sentInvoice(row.id)"><i class="far fa-check-circle mr-2"></i>{{ $t('invoices.mark_as_sent') }}</a>
															</div>
														</div>
													</td>
												</tr>
											
											</tbody>
                      <tbody v-else>
                        <tr class="text-center">
                          <td colspan="5">No records available</td>
                        </tr>
                      </tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="card">
								<div class="card-header">
									<div class="row">
										<div class="col">
											<h5 class="card-title">{{ $t('dashboard.recent_estimate_card.title') }}</h5>
										</div>
										<div class="col-auto">
											<router-link to="/admin/estimates" class="btn-right btn btn-sm btn-outline-primary">
												{{ $t('dashboard.recent_estimate_card.view_all') }}
											</router-link>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="mb-3">
										<div class="progress progress-md rounded-pill mb-3">
											<div class="progress-bar bg-success" role="progressbar" v-bind:style="{ width: estimatesData.sentCount+'%' }" :aria-valuenow="estimatesData.sentCount" aria-valuemin="0" aria-valuemax="100"></div>
											<div class="progress-bar bg-warning" role="progressbar" v-bind:style="{ width: estimatesData.draftCount+'%' }" :aria-valuenow="estimatesData.draftCount" aria-valuemin="0" aria-valuemax="100"></div>
                      <div class="progress-bar bg-danger" role="progressbar" v-bind:style="{ width: estimatesData.acceptedCount+'%' }"  :aria-valuenow="estimatesData.acceptedCount" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<div class="row">
											<div class="col-auto">
												<i class="fas fa-circle text-success mr-1"></i> Sent
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-warning mr-1"></i> Draft
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-danger mr-1"></i> Accepted
											</div>
										</div>
									</div>
									<div class="table-responsive">
										<table class="table table-hover">
											<thead class="thead-light">
												<tr>
													<th>{{$t('dashboard.recent_estimate_card.customer')}}</th>
													<th>{{ $t('dashboard.recent_estimate_card.date') }}</th>
													<th>{{$t('dashboard.recent_estimate_card.amount_due')}}</th>
													<th>{{$t('estimates.status')}}</th>
													<th class="text-right">Action</th>
												</tr>
											</thead>
											<tbody v-if="getRecentEstimates.length > 0">
												<tr v-for="row in getRecentEstimates" :key="row.id">
													<td>
														<h2 class="table-avatar">
															<router-link  :to="{ path: `customers/${row.user.id}/view` }" class="avatar avatar-sm mr-2"><img class="avatar-img rounded-circle" :src="$utils.userImage(row.user.avatar)" @error="altImg" alt="User Image"></router-link>
															 <router-link
                                :to="{ path: `invoices/${row.id}/view` }"
                                class="font-medium text-primary-500"
                              >
                              {{ row.user.name }}</router-link>
														</h2>
													</td>
													<td>{{ row.formattedExpiryDate }}</td>
													<td> <div v-html="$utils.formatMoney(row.total, row.user.currency)" /></td>
														<td><span  :style="{ 'background-color': `${$utils.getBadgeStatusColor(row.status).bgColor}`, 'color': $utils.getBadgeStatusColor(row.status).color }"> 
                              {{row.status}}</span></td>
													<td class="text-right">
														<div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<router-link class="dropdown-item" :to="`estimates/${row.id}/edit`"><i class="far fa-edit mr-2"></i>{{ $t('general.edit') }}</router-link>
																<a class="dropdown-item" @click="removeEstimate(row.id)"><i class="far fa-trash-alt mr-2"></i>{{ $t('general.delete') }}</a>
																<router-link class="dropdown-item" :to="`estimates/${row.id}/view`"><i class="far fa-eye mr-2"></i>{{ $t('general.view') }}</router-link>
																<a class="dropdown-item"  @click="convertInToinvoice(row.id)"><i class="far fa-file-alt mr-2"></i>{{ $t('estimates.convert_to_invoice') }}</a>
																<a class="dropdown-item" @click="onMarkAsSent(row.id)"><i class="far fa-check-circle mr-2"></i>{{ $t('estimates.mark_as_sent') }}</a>
																<!-- <a class="dropdown-item" @click="sendEstimate(row.id)" href="javascript:void(0);"><i class="far fa-paper-plane mr-2"></i>{{ $t('estimates.send_estimate') }}</a> -->
																<a class="dropdown-item"  v-if="row.status !== 'ACCEPTED'"   @click="onMarkAsAccepted(row.id)"><i class="far fa-check-circle mr-2"></i>{{ $t('estimates.mark_as_accepted') }}</a>
																<a class="dropdown-item"  v-if="row.status !== 'REJECTED'"  @click="onMarkAsRejected(row.id)"><i class="far fa-times-circle mr-2"></i>{{ $t('estimates.mark_as_rejected') }}</a>
															</div>
														</div>
													</td>
												</tr>
									
											</tbody>
                      <tbody v-else>
                          <tr class="text-center">  
                            <td colspan="5">No records available</td>
                          </tr>
                      </tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
            <SendEstimateModal ref="modal" />
          </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import SendEstimateModal from '../../components/base/modal/SendEstimateModal'

import {
  PencilIcon,
  EyeIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  DocumentTextIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    PencilIcon,
    EyeIcon,
    PaperAirplaneIcon,
    CheckCircleIcon,
    TrashIcon,
    DocumentTextIcon,
    XCircleIcon,
    SendEstimateModal,
  },

  data() {
    return {
      ...this.$store.state.dashboard,
      currency: {
        precision: 2,
        thousand_separator: ',',
        decimal_separator: '.',
        symbol: '$',
      },
      invoiceData: {
        paidCount: 0,
        unPaidCount: 0,
        partiallyPaidCount: 0
      },
      estimatesData: {
        draftCount: 0,
        sentCount: 0,
        acceptedCount: 0
      },
      isLoaded: false,
      fetching: false,
      isRequestOngoing: false,
    }
  },

  computed: {
    ...mapGetters('user', {
      user: 'currentUser',
    }),

    ...mapGetters('dashboard', [
      'getDashboardDataLoaded',
      'getDueInvoices',
      'getRecentEstimates',
    ]),
  },
  mounted() {
     this.loadData()
  },
  methods: {
    ...mapActions('dashboard', ['loadData']),

    ...mapActions('invoice', ['deleteInvoice', 'sendEmail', 'markAsSent']),

    ...mapActions('estimate', [
      'deleteEstimate',
      'markAsAccepted',
      'markAsRejected',
      'convertToInvoice',
    ]),

    ...mapActions('estimate', {
      sendEstimateEmail: 'sendEmail',
      markEstimateAsSent: 'markAsSent',
    }),

    async loadData(params) {
      const {dashboard} = this.$store.state

      this.isRequestOngoing = true
      await this.$store.dispatch('dashboard/loadData', params)
      this.invoiceData.paidCount = dashboard.invoiceData.paidCount
      this.invoiceData.unPaidCount = dashboard.invoiceData.unPaidCount
      this.invoiceData.partiallyPaidCount = dashboard.invoiceData.partiallyPaidCount
      this.estimatesData.draftCount = dashboard.estimatesData.draftCount
      this.estimatesData.sentCount = dashboard.estimatesData.sentCount
      this.estimatesData.acceptedCount = dashboard.estimatesData.acceptedCount
      this.isLoaded = true
      this.isRequestOngoing = false

    },

    async removeEstimate(id) {
      this.id = id
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('estimates.confirm_delete', 1),
          
          buttons: true,
          dangerMode: true,
        })
        .then(async (willDelete) => {
          if (willDelete) {
            let res = await this.deleteEstimate({ ids: [this.id] })
            if (res.data.success) {
              window.toastr['success'](this.$tc('estimates.deleted_message', 1))
              this.loadData()
            } else if (res.data.error) {
              window.toastr['error'](res.data.message)
            }
          }
        })
    },

    refreshInvTable() {
     this.loadData()
    },

    refreshEstTable() {
     this.loadData()
    },
    altImg(e) {
      e.target.src = '/assets/img/default-avatar.jpg'
    },
    async convertInToinvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_conversion'),
          
          buttons: true,
          dangerMode: true,
        })
        .then(async (willDelete) => {
          if (willDelete) {
            let res = await this.convertToInvoice(id)
            this.selectAllField = false

            if (res.data) {
              window.toastr['success'](this.$t('estimates.conversion_message'))
              this.$router.push(`invoices/${res.data.invoice.id}/edit`)
            } else if (res.data.error) {
              window.toastr['error'](res.data.message)
            }
          }
        })
    },

    async onMarkAsSent(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_mark_as_sent'),
          
          buttons: true,
          dangerMode: true,
        })
        .then(async (willMarkAsSent) => {
          if (willMarkAsSent) {
            const data = {
              id: id,
              status: 'SENT',
            }

            let response = await this.markEstimateAsSent(data)
            this.loadData()

            if (response.data) {
              window.toastr['success'](
                this.$tc('estimates.mark_as_sent_successfully')
              )
            }
          }
        })
    },

    async removeInvoice(id) {
      this.id = id
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('invoices.confirm_delete'),
         
          buttons: true,
          dangerMode: true,
        })
        .then(async (willDelete) => {
          if (willDelete) {
            let res = await this.deleteInvoice({ ids: [this.id] })
            if (res.data.success) {
              window.toastr['success'](this.$tc('invoices.deleted_message'))
              this.refreshInvTable()
            } else if (res.data.error) {
              window.toastr['error'](res.data.message)
            }
          }
        })
    },

    async sendInvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('invoices.confirm_send'),
         
          buttons: true,
          dangerMode: true,
        })
        .then(async (willSendInvoice) => {
          if (willSendInvoice) {
            const data = {
              id: id,
            }
            let response = await this.sendEmail(data)
            this.refreshInvTable()

            if (response.data.success) {
              window.toastr['success'](
                this.$tc('invoices.send_invoice_successfully')
              )
              return true
            }

            if (response.data.error === 'user_email_does_not_exist') {
              window.toastr['error'](
                this.$tc('invoices.user_email_does_not_exist')
              )
              return false
            }
            window.toastr['error'](this.$tc('invoices.something_went_wrong'))
          }
        })
    },

    async sentInvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('invoices.invoice_mark_as_sent'),
          
          buttons: true,
          dangerMode: true,
        })
        .then(async (willMarkAsSend) => {
          if (willMarkAsSend) {
            const data = {
              id: id,
              status: 'SENT',
            }
            let response = await this.markAsSent(data)

            this.refreshInvTable()
            if (response.data) {
              window.toastr['success'](
                this.$tc('invoices.mark_as_sent_successfully')
              )
            }
          }
        })
    },

    async onMarkAsAccepted(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_mark_as_accepted'),
          
          buttons: true,
          dangerMode: true,
        })
        .then(async (markedAsRejected) => {
          if (markedAsRejected) {
            const data = {
              id: id,
            }
            let response = await this.markAsAccepted(data)
            this.refreshEstTable()

            if (response.data) {
              this.refreshEstTable()
              window.toastr['success'](
                this.$tc('estimates.marked_as_accepted_message')
              )
            }
          }
        })
    },

    async onMarkAsRejected(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_mark_as_rejected'),
          
          buttons: true,
          dangerMode: true,
        })
        .then(async (markedAsRejected) => {
          if (markedAsRejected) {
            const data = {
              id: id,
            }
            let response = await this.markAsRejected(data)
            this.refreshEstTable()

            if (response.data) {
              this.refreshEstTable()
              window.toastr['success'](
                this.$tc('estimates.marked_as_rejected_message')
              )
            }
          }
        })
    },

    async sendEstimate(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_send_estimate'),
         
          buttons: true,
          dangerMode: true,
        })
        .then(async (willSendEstimate) => {
          if (willSendEstimate) {
            const data = {
              id: id,
            }
            let response = await this.sendEstimateEmail(data)
            this.loadData()

            if (response.data.success) {
              window.toastr['success'](
                this.$tc('estimates.send_estimate_successfully')
              )
              return true
            }

            if (response.data.error === 'user_email_does_not_exist') {
              window.toastr['error'](
                this.$tc('estimates.user_email_does_not_exist')
              )
              return true
            }
            window.toastr['error'](this.$tc('estimates.something_went_wrong'))
          }
        })
    },
  },
}
</script>
