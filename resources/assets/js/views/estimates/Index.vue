<template>
  <base-page>
    <div class="content container-fluid">
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">{{$tc('estimates.estimate')}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link>
									</li>
									<li class="breadcrumb-item active">{{$tc('estimates.estimate')}}</li>
								</ul>
							</div>
							<div class="col-auto">
								<router-link to="/admin/estimates/create" class="btn btn-primary mr-1">
									<i class="fas fa-plus"></i>
								</router-link>
								<a class="btn btn-primary filter-btn" v-if="totalEstimates"  @click="toggleFilter(1)"  id="filter_search">
									<i class="fas fa-filter"></i>
								</a>
							</div>
						</div>
					</div>
					
					<!-- Search Filter -->
					<div class="card filter-card"  v-show="showFilters">
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
										<label>{{$t('estimates.status')}}</label>
										<select class="select form-control" v-model="filters.status">
											<option>{{$t('general.select_a_status')}}</option>
											<option v-for="(item, index) in status" :key="index">{{item}}</option>
										</select>
									</div>
								</div>
                 <div class="col-md-3">
                  <div class="form-group">
                    <label>{{$t('estimates.estimate_number')}}</label>
                    <input type="text" v-model="filters.estimate_number" class="form-control">
                  </div>
                </div>
								
							</div>
              <div class="row">
              <div class="col-md-3">
                  <div class="form-group">
                    <label>{{$t('general.from')}}</label>
                          <div class="datemx">
                      <datepicker v-model="filters.from_date"
                          :disabled-date="$utils.disabledAfterToday" 
                          value-type="format" format="YYYY-MM-DD">
                      </datepicker>
                    </div>
                  </div>
                </div>
              <div class="col-md-3">
                  <div class="form-group">
                    <label>{{$t('general.to')}}</label>
                      <div class="datemx">
                        <datepicker v-model="filters.to_date"
                            :disabled-date="$utils.disabledAfterToday" 
                            value-type="format" format="YYYY-MM-DD">
                        </datepicker>
                      </div>
                  </div>
                </div>
               
                </div>
						</div>
            <label
          class="absolute text-sm leading-snug text-black cursor-pointer text-danger"
          style="top: 10px;right: 15px;position:absolute"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}</label>
					</div>
					<!-- /Search Filter -->
					<div class="row mt-4">
            <base-loader v-if="isRequestOngoing" :show-bg-overlay="true" />
              <div class="col-md-12">
                   <ul class="nav nav-tabs ml-1">
                      <li class="nav-item">
                        <a class="nav-link" :class="{'active': filters.status==='DRAFT' }" @click="filters.status='DRAFT'" >{{$t('general.draft')}}</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" :class="{'active': filters.status==='SENT' }" @click="filters.status='SENT'">{{$t('general.sent')}}</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" :class="{'active': filters.status!=='SENT' && filters.status!=='DRAFT' }" @click="filters.status=''">{{$t('general.all')}}</a>
                      </li>
                  </ul>
                  <div class="row">
                  <div class="col-sm-12">
              <div class="custom_Card">
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
                   <template slot="estimate_number" slot-scope="props">
                      <router-link
                      :to="{ path: `estimates/${props.rowData.id}/view` }"
                      class="font-medium text-primary-500">
                      {{ props.rowData.estimate_number }}
                    </router-link>
                  </template>
                  <template slot="total" slot-scope="props">
                    <div v-html="$utils.formatMoney(props.rowData.total, props.rowData.user.currency)" />
                  </template>
                  <template slot="status" slot-scope="props">
                    <span  :style="{ 'background-color': `${$utils.getBadgeStatusColor(props.rowData.status).bgColor}`, 'color': $utils.getBadgeStatusColor(props.rowData.status).color }"> {{props.rowData.status.replace('_', ' ')}}</span>
                  </template>
                  <template slot="name" slot-scope="props">
                      <router-link  :to="{ path: `customers/${props.rowData.user.id}/view` }" class="avatar avatar-sm mr-2"><img class="avatar-img rounded-circle" :src="$utils.userImage(props.rowData.user.avatar)" @error="altImg" alt="User Image"></router-link>
                        <router-link  :to="{ path: `customers/${props.rowData.user.id}/view` }">{{props.rowData.name}}</router-link>
                  </template>
                  <template :slot="$t('general.actions')" slot-scope="props">
                    <div class="table-button-container">
                        <div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<router-link class="dropdown-item" :to="`estimates/${props.rowData.id}/edit`"><i class="far fa-edit mr-2"></i>{{ $t('general.edit') }}</router-link>
																<a class="dropdown-item" href="javascript:void(0);" @click="removeEstimate(props.rowData.id)"><i class="far fa-trash-alt mr-2"></i>{{ $t('general.delete') }}</a>
																<router-link class="dropdown-item" :to="`estimates/${props.rowData.id}/view`" ><i class="far fa-eye mr-2" ></i>{{ $t('general.view') }}</router-link>
																<a class="dropdown-item" href="javascript:void(0);" @click="convertInToinvoice(props.rowData.id)"><i class="far fa-file-alt mr-2" ></i> {{ $t('estimates.convert_to_invoice') }}</a>
																<a class="dropdown-item" href="javascript:void(0);" @click="onMarkAsSent(props.rowData.id)" v-if="props.rowData.status !== 'SENT'"><i class="far fa-check-circle mr-2"   ></i>{{ $t('estimates.mark_as_sent') }}</a>
																<a class="dropdown-item" href="javascript:void(0);" @click="sendEstimate(props.rowData.id, props.rowData)" v-if="props.rowData.status !== 'SENT'"><i class="far fa-paper-plane mr-2" > </i>{{ $t('estimates.send_estimate') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);" @click="sendEstimate(props.rowData.id, props.rowData)" v-if="props.rowData.status == 'SENT' || props.rowData.status == 'VIEWED'"><i class="far fa-paper-plane mr-2"></i>{{ $t('estimates.resend_estimate') }}</a>
																<a class="dropdown-item" href="javascript:void(0);"  @click="onMarkAsAccepted(props.rowData.id)" v-if="props.rowData.status !== 'ACCEPTED'"><i class="far fa-check-circle mr-2"  ></i>{{ $t('estimates.mark_as_accepted') }}</a>
																<a class="dropdown-item" href="javascript:void(0);" v-if="props.rowData.status !== 'REJECTED'" @click="onMarkAsRejected(props.rowData.id)"><i class="far fa-times-circle mr-2"></i>{{ $t('estimates.mark_as_rejected') }}</a>
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
          </div>
            <SendEstimateModal  ref="modal"/> 
				</div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  XCircleIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  PencilIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'
import SendEstimateModal from '../../components/base/modal/SendEstimateModal'

import { DotsHorizontalIcon } from '@vue-hero-icons/outline'
import TableStyle from '../../tablestyle/tablestyle'
export default {
  components: {
    DotsHorizontalIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    DocumentTextIcon,
    XCircleIcon,
    EyeIcon,
    HashtagIcon,
    SendEstimateModal
  },

  data() {
    return {
      showFilters: false,
      currency: null,
      status: ['DRAFT', 'SENT', 'VIEWED', 'EXPIRED', 'ACCEPTED', 'REJECTED'],
      isRequestOngoing: true,
      activeTab: this.$t('general.draft'),
      showNomessage: false,
      perPage: 10,
      page: 1,
      itemsList: [],
      totalCount: 0,
      css: TableStyle,
      sortOrder: [
        {
            field: 'created_at',
            sortField: 'created_at',
            direction: 'desc'
        }
      ],
      filters: {
        customer: '',
        status: 'DRAFT',
        from_date: '',
        to_date: '',
        estimate_number: '',
      },
      estimateList: [],
      fields: [
        { 
          name: 'estimate_number', 
          title: this.$t('estimates.estimate_number'),
          sortField: 'estimate_number',
        }, 
        {
          name: 'name',
          title: this.$t('estimates.customer'),
          sortField: 'name',
        },
        {
          name: 'formattedEstimateDate',
          title: this.$t('estimates.date'),
          sortField: 'estimate_date'
        },
        {
          name: 'status',
          title: this.$t('estimates.status'),
          sortField: 'status',
        },
         {
          name: 'total',
          title: this.$t('estimates.total'),
          sortField: 'total',
        },
        this.$t('general.actions')
      ],
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalEstimates && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('estimate', [
      'selectedEstimates',
      'totalEstimates',
      'estimates',
      'selectAllField',
    ]),

    selectField: {
      get: function () {
        return this.selectedEstimates
      },
      set: function (val) {
        this.selectEstimate(val)
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
    estimateList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllEstimates()
    }
  },
  created() {
    this.fetchData(this.page, this.sortOrder)
    this.fetchCustomers()
  },
  methods: {
    ...mapActions('estimate', [
      'fetchEstimates',
      'resetSelectedEstimates',
      'getRecord',
      'selectEstimate',
      'selectAllEstimates',
      'deleteEstimate',
      'deleteMultipleEstimates',
      'markAsSent',
      'convertToInvoice',
      'setSelectAllState',
      'markAsAccepted',
      'markAsRejected',
      'sendEmail',
    ]),
    
     ...mapActions('customer', [
      'fetchCustomers'
    ]),

    ...mapActions('modal', ['openModal']),

    refreshTable() {
      this.fetchData(this.page=1, this.sortOrder)
    },

    async fetchData(page, sort) {
      let data = {
        customer_id:
          this.filters.customer === ''
            ? this.filters.customer
            : this.filters.customer.id,
        status: this.filters.status,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        estimate_number: this.filters.estimate_number,
        orderByField: sort && sort.fieldName || 'created_at',
        orderBy: sort && sort.order || 'desc',
        page,
      }
      this.showNomessage = false
      

      this.isRequestOngoing = true
      let response = await this.fetchEstimates(data)
      
      this.isRequestOngoing = false

      this.currency = response.data.currency
      if(response.data.estimates.data.length === 0) this.showNomessage = true
      this.estimateList = response.data.estimates.data
      this.totalCount = response.data.estimates.total

    },
    altImg(e) {
      e.target.src = '/assets/img/default-avatar.jpg'
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
      let local = this.estimateList;

      // sortOrder can be empty, so we have to check for that as well
      if (sortOrder.length > 0) {
        local = _.orderBy(
          local,
          sortOrder[0].sortField,
          sortOrder[0].direction
        );
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
    refreshTable() {
      this.$nextTick(() => {
          this.fetchData(this.page=1,this.sortOrder)
      })
    },
    setStatusFilter(val) {
      if (this.activeTab == val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('general.draft'):
          this.filters.status = 'DRAFT'
          break
        case this.$t('general.sent'):
          this.filters.status = 'SENT'
          break
        default:
          this.filters.status = ''
          break
      }
    },

    async onMarkAsAccepted(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_accepted'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id,
            status: 'ACCEPTED',
          }

          let response = await this.markAsAccepted(data)

          if (response.data) {
            this.refreshTable()
            window.toastr['success'](
              this.$tc('estimates.marked_as_accepted_message')
            )
          }
        }
      })
    },

    async onMarkAsRejected(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_rejected'),
       
        buttons: true,
        dangerMode: true,
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id,
            status: 'REJECTED',
          }

          let response = await this.markAsRejected(data)

          if (response.data) {
            this.refreshTable()
            window.toastr['success'](
              this.$tc('estimates.marked_as_rejected_message')
            )
          }
        }
      })
    },
   
    setFilters() {
      this.resetSelectedEstimates()
      this.refreshTable()
    },
    refreshTable() {
      this.fetchData(1, 'created_at')
    },
    clearFilter() {
      this.filters = {
        customer: '',
        status: '',
        from_date: '',
        to_date: '',
        estimate_number: '',
      }

      this.activeTab = this.$t('general.all')
    },

    toggleFilter(value) {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },
    async removeEstimate(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 1),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteEstimate({ ids: [this.id] })

          if (res.data.success) {
            //this.resetSelectedEstimates()
            window.toastr['success'](this.$tc('estimates.deleted_message', 1))
            this.refreshTable()
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async convertInToinvoice(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_conversion'),
       
        buttons: true,
        dangerMode: true,
      }).then(async (willConvertInToinvoice) => {
        if (willConvertInToinvoice) {
          let res = await this.convertToInvoice(id)

          if (res.data) {
            window.toastr['success'](this.$t('estimates.conversion_message'))
            this.$router.push(`invoices/${res.data.invoice.id}/edit`)
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async removeMultipleEstimates() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 2),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteMultipleEstimates()

          if (res.data.success) {
            this.$refs.table.refresh()
            this.resetSelectedEstimates()
            window.toastr['success'](this.$tc('estimates.deleted_message', 2))
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

    async onMarkAsSent(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_sent'),
       
        buttons: true,
        dangerMode: true,
      }).then(async (willMarkAsSent) => {
        if (willMarkAsSent) {
          const data = {
            id: id,
            status: 'SENT',
          }

          let response = await this.markAsSent(data)
          this.refreshTable()

          if (response.data) {
            window.toastr['success'](
              this.$tc('estimates.mark_as_sent_successfully')
            )
          }
        }
      })
    },
    sendEstimate(id, estimate) {
      this.$refs.modal.show(id, estimate)
    },
    setActiveTab(val) {
      switch (val) {
        case 'DRAFT':
          this.activeTab = this.$t('general.draft')
          break
        case 'SENT':
          this.activeTab = this.$t('general.sent')
          break
        default:
          this.activeTab = this.$t('general.all')
          break
      }
    },
  },
}
</script>
