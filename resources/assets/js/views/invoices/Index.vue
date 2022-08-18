<template>
  <base-page>
    <div class="content container-fluid">
					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">{{$t('invoices.title')}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link></li>
									<li class="breadcrumb-item active">{{$tc('invoices.invoice', 2)}}</li>
								</ul>
							</div>
							<div class="col-auto">
								<router-link  to="/admin/invoices/create" class="btn btn-primary">
									<i class="fas fa-plus"></i>
								</router-link>
								<a class="btn btn-primary filter-btn" v-show="totalInvoices" @click="toggleFilter" id="filter_search">
									<i class="fas fa-filter"></i>
								</a>
							</div>
						</div>
             
					</div>
					<!-- /Page Header -->
			   
					<!-- Search Filter -->
					<div  class="card filter-card" v-show="showFilters">
						<div class="card-body pb-0">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
									<label>{{$tc('customers.customer', 1)}}</label>
											<select class="select form-control"  v-model="filters.customer">
											<option>Select Customer</option>
                      <option v-for="item in customers" :key="item.id" v-bind:value="{'id': item.id, 'name': item.id}">{{item.name}}</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>{{$tc('invoices.status', 1)}}</label>
										<select class="select form-control" v-model="filters.status">
											<option>{{$t('general.select_a_status')}}</option>
											<option v-for="(item,index) in status" :key="index" v-bind:value="item">{{item}}</option>
										</select>
									</div>
								</div>
                  <div class="col-md-3">
                  <div class="form-group">
                    <label>{{$t('invoices.invoice_number')}}</label>
                    <input type="text" v-model="filters.invoice_number" class="form-control">
                  </div>
                </div>
								
							</div>
              <div class="row">
              <div class="col-md-3">
                  <div class="form-group">
                    <label>{{$t('general.from')}}</label>
                    <div class="datemx">
                          <datepicker v-model="filters.from_date"  :disabled-date="$utils.disabledAfterToday" value-type="format" format="YYYY-MM-DD">
                          </datepicker>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>{{$t('general.to')}}</label>
                    <div class="datemx">
                        <datepicker v-model="filters.to_date" :disabled-date="$utils.disabledAfterToday" value-type="format" format="YYYY-MM-DD">
                        </datepicker>
                    </div>
                  </div>
                </div>
              </div>
						</div>
            <label
                class="absolute text-sm leading-snug text-black cursor-pointer text-danger"
                style="top: 10px;right: 15px;position:absolute"
                @click="clearFilter">{{ $t('general.clear_all') }}
            </label>
					</div>
					<!-- /Search Filter -->
					
					<div class="row">
            <base-loader v-if="isRequestOngoing" :show-bg-overlay="true" />
						<div class="col-sm-12">
            <div class="custom_Card">
               <ul class="nav nav-tabs ml-1">
                    <li class="nav-item">
                      <a class="nav-link" :class="{'active': filters.status==='DUE' }" @click="filters.status='DUE'" >{{$t('general.due')}}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" :class="{'active': filters.status==='DRAFT' }" @click="filters.status='DRAFT'">{{$t('general.draft')}}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" :class="{'active': filters.status!=='DUE' && filters.status!=='DRAFT' }" @click="filters.status=''">{{$t('general.all')}}</a>
                    </li>
                </ul>
              
							<div class="card card-table"> 
								<div class="card-body">
									<div class="table-responsive">
                     <vuetable ref="vuetable"
                      :api-mode="false"
                      :fields="fields"
                      :per-page="perPage"
                      pagination-path="pagination"
                      :sort-order="sortOrder"
                      @vuetable:pagination-data="onPaginationData"
                      :no-data-template="showNomessage ? 'No records available' : ''"    
                      :data-manager="dataManager"
                      :css="css.table"
                    >
                   <template slot="invoice_number" slot-scope="props">
                    <router-link
                      :to="{ path: `invoices/${props.rowData.id}/view` }"
                      class="font-medium text-primary-500"
                      >
                        {{ props.rowData.invoice_number }}
                    </router-link>
                  </template>
                  <template slot="name" slot-scope="props">
                    <router-link  :to="{ path: `customers/${props.rowData.user.id}/view` }" class="avatar avatar-sm mr-2"><img class="avatar-img rounded-circle" :src="$utils.userImage(props.rowData.user.avatar)" alt="User Image"></router-link>
                    <router-link  :to="{ path: `customers/${props.rowData.user.id}/view` }">{{props.rowData.name}}</router-link>
                  </template>
                   <template slot="due_amount" slot-scope="props">
                    <div v-html="$utils.formatMoney(props.rowData.due_amount,props.rowData.user.currency)" />
                  </template>
                  <template slot="status" slot-scope="props">
                    <span  :style="{ 'background-color': `${$utils.getBadgeStatusColor(props.rowData.status).bgColor}`, 'color': $utils.getBadgeStatusColor(props.rowData.status).color }"> {{props.rowData.status.replace('_', ' ')}}</span>
                  </template>
                  <template slot="paid_status" slot-scope="props">
                    <span  :style="{ 'background-color': `${$utils.getBadgeStatusColor(props.rowData.status).bgColor}`, 'color': $utils.getBadgeStatusColor(props.rowData.status).color }"> {{props.rowData.paid_status.replace('_', ' ')}}</span>
                  </template>
                  <template :slot="$t('invoices.action')" slot-scope="props">
                    <div class="table-button-container">
                        <div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
                                  <router-link class="dropdown-item" :to="`invoices/${props.rowData.id}/edit`" ><i class="far fa-edit mr-2" ></i>{{ $t('general.edit') }}</router-link>
                                  <a class="dropdown-item" href="javascript:void(0);" @click="removeInvoice(props.rowData.id)"><i class="far fa-trash-alt mr-2"></i>{{ $t('general.delete') }}</a>
                                  <router-link class="dropdown-item" :to="`invoices/${props.rowData.id}/view`" ><i class="far fa-eye mr-2" ></i>{{ $t('invoices.view') }}</router-link>
                                  <a class="dropdown-item" href="javascript:void(0);" @click="markInvoiceAsSent(props.rowData.id)"  v-if="props.rowData.status !== 'SENT'" ><i class="far fa-check-circle mr-2" ></i>{{ $t('invoices.mark_as_sent') }}</a>
                                  <a class="dropdown-item" href="javascript:void(0);" @click="sendInvoice(props.rowData.id, props.rowData)" v-if="props.rowData.status !== 'SENT'"><i class="far fa-paper-plane mr-2"  ></i>{{ $t('invoices.send_invoice') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);"  @click="sendInvoice(props.rowData.id, props.rowData)"   v-if="props.rowData.status == 'SENT' || props.rowData.status == 'VIEWED'"><i class="far fa-paper-plane mr-2" ></i>{{ $t('invoices.resend_invoice') }}</a>
                                 <a class="dropdown-item" href="javascript:void(0);"  @click="onCloneInvoice(props.rowData.id)"><i class="far fa-clone mr-2" ></i>{{ $t('invoices.clone_invoice') }}</a>
                                 <router-link class="dropdown-item"  v-if=" props.rowData.status === 'SENT' ||  props.rowData.status === 'VIEWED' || props.rowData.status === 'OVERDUE'" :to="`/admin/payments/${props.rowData.id}/create`"><i class="fa fa-credit-card" aria-hidden="true"></i> {{ $t('payments.record_payment') }}</router-link>
															</div>
                        </div>
                    </div>
                    </template>
                  </vuetable>
                  <vuetable-pagination ref="pagination"
                    @vuetable-pagination:change-page="onChangePage" :css="css.pagination"
                  ></vuetable-pagination>
									</div>
								</div>
							</div>
              </div>
						</div>
					</div>
				</div>
        <SendInvoiceModal ref="modal"/>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import TableStyle from '../../tablestyle/tablestyle'
import SendInvoiceModal from '../../components/base/modal/SendInvoiceModal'
import {
  PencilIcon,
  DocumentDuplicateIcon,
  CreditCardIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  PlusIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  XCircleIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

import { DotsHorizontalIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    DotsHorizontalIcon,
    PencilIcon,
    DocumentDuplicateIcon,
    TrashIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    DocumentTextIcon,
    XCircleIcon,
    EyeIcon,
    CreditCardIcon,
    HashtagIcon,
    SendInvoiceModal,
  },

  data() {
    return {
      showFilters: false,
      currency: null,
      invoiceList: [],
      status: ['DRAFT', 'DUE', 'SENT', 'VIEWED', 'OVERDUE', 'COMPLETED','UNPAID','PAID','PARTIALLY_PAID'],
      perPage: 10,
      page: 1,
      totalCount: 0,
      css: TableStyle,
      sortOrder: [
        {
            field: 'created_at',
            sortField: 'created_at',
            direction: 'desc'
        }
      ],
      fields: [
        { 
          name: 'invoice_number', 
          title: this.$t('invoices.number'),
          sortField: 'invoice_number',
        }, 
        {
          name: 'name',
          title: this.$t('invoices.customer'),
          sortField: 'name',
        },
        {
          name: 'formattedInvoiceDate',
          title: this.$t('invoices.date'),
          sortField: 'invoice_date'
        },
        {
          name: 'due_amount',
          title: this.$t('invoices.amount_due'),
          sortField: 'due_amount',
        },
         {
          name: 'status',
          title: this.$t('invoices.status'),
          sortField: 'status'
        },
         {
          name: 'paid_status',
          title: this.$t('invoices.paid_status'),
          sortField: 'paid_status'
        },
        this.$t('invoices.action')
      ],
      showNomessage: false,
      isRequestOngoing: true,
      activeTab: this.$t('general.due'),
      filters: {
        customer: '',
        status: 'DUE',
        from_date: '',
        to_date: '',
        invoice_number: '',
      },
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalInvoices && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('invoice', [
      'selectedInvoices',
      'totalInvoices',
      'invoices',
      'selectAllField',
    ]),

    selectField: {
      get: function () {
        return this.selectedInvoices
      },
      set: function (val) {
        this.selectInvoice(val)
      },
    },

    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
      },
    },
  },

  watch: {
    filters: {
      handler: _.debounce(function() {
       this.refreshTable();
      }, 500),
      deep: true,
    },
    invoiceList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },
  created() {
      this.fetchData(this.page, this.sortOrder)
      this.fetchCustomers()
  },
  destroyed() {
    if (this.selectAllField) {
      this.selectAllInvoices()
    }
  },

  methods: {
    ...mapActions('invoice', [
      'fetchInvoices',
      'getRecord',
      'selectInvoice',
      'resetSelectedInvoices',
      'selectAllInvoices',
      'deleteInvoice',
      'deleteMultipleInvoices',
      'sendEmail',
      'markAsSent',
      'setSelectAllState',
      'cloneInvoice',
    ]),
    ...mapActions('customer', ['fetchCustomers']),

    ...mapActions('modal', ['openModal']),

    async sendInvoice(id, invoice) {
      this.$refs.modal.show(id, invoice)
    },

    async markInvoiceAsSent(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          const data = {
            id: id,
            status: 'SENT',
          }
          let response = await this.markAsSent(data)
          this.refreshTable()
          if (response.data) {
            window.toastr['success'](
              this.$tc('invoices.mark_as_sent_successfully')
            )
          }
        }
      })
    },
    refreshTable() {
        this.fetchData(this.page=1,this.sortOrder)
    },
    async onCloneInvoice(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_clone'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.cloneInvoice({ id })

          this.refreshTable()

          if (response.data) {
            window.toastr['success'](this.$tc('invoices.cloned_successfully'))
            this.$router.push(
              `/admin/invoices/${response.data.invoice.id}/edit`
            )
          }
        }
      })
    },

    refreshTable() {
      this.$nextTick(() => {
          this.fetchData(this.page=1,this.sortOrder)
      })
    },

    async fetchData(page, sort) {
      let data = {
        customer_id: this.filters.customer ? this.filters.customer.id : '',
        status: this.filters.status,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        invoice_number: this.filters.invoice_number,
        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort && sort[0].direction || 'desc',
        page,
      }
      this.showNomessage = false;
      this.isRequestOngoing = true
      let response = await this.fetchInvoices(data)
      this.isRequestOngoing = false
      this.showNomessage = true;
      this.currency = response.data.currency
      this.invoiceList = response.data.invoices.data
      this.totalCount = response.data.invoices.total
    },

    setFilters() {
      this.resetSelectedInvoices()
      //this.refreshTable()
    },

    clearFilter() {

      this.filters = {
        customer: '',
        status: '',
        from_date: '',
        to_date: '',
        invoice_number: '',
      }

      this.activeTab = this.$t('general.all')
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },
     onPaginationData (paginationData) {
     this.$refs.pagination.setPaginationData(paginationData)
    },
    async onChangePage (page) {
      this.page = page;
      await this.fetchData(page,this.sortOrder);
      this.$refs.vuetable.changePage(page)
    },
    dataManager(sortOrder, pagination) {
      let local = this.invoiceList;
      
      // sortOrder can be empty, so we have to check for that as well
      if (sortOrder.length > 0) {
        local = _.orderBy(
          local,
          sortOrder[0].sortField,
          sortOrder[0].direction
        );
        //this.fetchData(this.page,sortOrder);
      }
      
      pagination = this.$refs.vuetable.makePagination(
        this.totalCount,
        this.perPage
      );
     
      return {
        pagination: pagination,
        data: local
      };
    },
    async removeInvoice(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteInvoice({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message'))
            this.refreshTable()
            return true
          }

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          window.toastr['error'](res.data.error)
          return true
        }
        this.resetSelectedInvoices()
      })
    },

    async removeMultipleInvoices() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete', 2),
        
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteMultipleInvoices()

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          if (res.data) {
            this.refreshTable()
            this.resetSelectedInvoices()
            window.toastr['success'](this.$tc('invoices.deleted_message', 2))
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      this.refreshTable()
    },

    async clearStatusSearch(removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    },

    setActiveTab(val) {
      switch (val.value) {
        case 'DRAFT':
          this.activeTab = this.$t('general.draft')
          break
        case 'DUE':
          this.activeTab = this.$t('general.due')
          break
        default:
          this.activeTab = this.$t('general.all')
          break
      }
    },
  },
}
</script>
