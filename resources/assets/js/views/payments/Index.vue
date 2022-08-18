<template>
  <base-page class="payments">
      <div class="content container-fluid">
			
					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">{{$tc('payments.title')}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$tc('general.home')}}</router-link></li>
									<li class="breadcrumb-item active">{{$tc('payments.payment', 2)}}</li>
								</ul>
							</div>
							<div class="col-auto">
								<router-link to="/admin/payments/create"  class="btn btn-primary mr-1">
									<i class="fas fa-plus"></i>
								</router-link>
								<a class="btn btn-primary filter-btn" v-show="totalPayments" href="javascript:void(0);" @click="toggleFilter" id="filter_search">
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
								<div class="col-sm-6 col-md-3">
									<div class="form-group">
										<label>{{$t('payments.customer')}}</label>                    
                    	<select class="select form-control"  v-model="filters.customer">
											<option>Select Customer</option>
                      <option v-for="item in customers" :key="item.id" v-bind:value="{'id': item.id, 'name': item.id}">{{item.name}}</option>
										</select>
									</div>
								</div>
								<div class="col-sm-6 col-md-3">
									<div class="form-group">
										<label>{{$t('payments.payment_number')}}</label>
										<input type="text" v-model="filters.payment_number" class="form-control">
									</div>
								</div>
								<div class="col-sm-6 col-md-3">
									<div class="form-group">
										<label>{{$t('payments.payment_mode')}}</label>
										<select class="select form-control" v-model="filters.payment_mode">
											<option>{{$t('payments.payment_mode')}}</option>
                      <option v-for="(item,index) in paymentModes" :key="index" v-bind:value="{'id': item.id, 'name': item.id}">{{item.name}}</option>
										</select>
									</div>
								</div>
							</div>
						</div>
             <label
          class="absolute text-sm leading-snug text-black cursor-pointer text-danger"
          style="top: 10px;right: 15px;position:absolute"
          @click="clearFilter"  v-show="totalPayments"
          >{{ $t('general.clear_all') }}</label>
					</div>
					<!-- /Search Filter -->
					
					<div class="row">
						<div class="col-sm-12">
               <base-loader v-if="isRequestOngoing" :show-bg-overlay="true" />
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
                  <template slot="amount" slot-scope="props">
                       <div v-html="$utils.formatMoney(props.rowData.amount, props.rowData.user.currency)" />
                  </template>
                  <template slot="invoice_number" slot-scope="props">
                       <span>{{props.rowData.invoice_number}}</span>
                  </template>
                   <template slot="payment_mode" slot-scope="props">
                       <span>{{props.rowData.payment_mode}}</span>
                  </template>
                  <template slot="payment_number" slot-scope="props">
                        <router-link
                              :to="{ path: `payments/${props.rowData.id}/view` }"
                              class="font-medium text-primary-500"
                        >{{ props.rowData.payment_number }}</router-link>
                  </template>
                  <template slot="name" slot-scope="props">
                    <router-link  :to="{ path: `customers/${props.rowData.user.id}/view` }" class="avatar avatar-sm mr-2"><img class="avatar-img rounded-circle" :src="$utils.userImage(props.rowData.user.avatar)" alt="User Image"></router-link>
                    <router-link  :to="{ path: `customers/${props.rowData.user.id}/view` }">{{props.rowData.name}}</router-link>
                  </template>
                  <template :slot="$t('general.actions')" slot-scope="props">
                    <div class="table-button-container">
                        <router-link :to="`payments/${props.rowData.id}/edit`" class="btn btn-sm btn-white text-success mr-2">
                          <i class="fas fa-edit mr-1"></i> {{ $t('general.edit') }}
                        </router-link>
                          <router-link :to="`payments/${props.rowData.id}/view`" class="btn btn-sm btn-white text-info">
                          <i class="far fa-eye mr-1"></i> {{ $t('general.view') }}
                        </router-link>
                        <a @click="removePayment(props.rowData.id)" class="btn btn-sm btn-white text-danger">
                          <i class="fas fa-trash-alt mr-1"></i> {{ $t('general.delete') }}
                        </a>
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
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import TableStyle from '../../tablestyle/tablestyle'


export default {
  

  data() {
    return {
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      loading: false,
      paymentList: [],
      customerSelect: null,
      showNomessage: false,
      perPage: 10,
      page: 1,
      paymentsList: [],
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
          name: 'payment_number', 
          title: this.$t('payments.payment_number'),
          sortField: 'name',
        }, 
        {
          name: 'name',
          title: this.$t('payments.customer'),
          sortField: 'name',
        },
        {
          name: 'formattedPaymentDate',
          title: this.$t('payments.date'),
          sortField: 'payment_date'
        },
        {
          name: 'amount',
          title: this.$t('payments.amount'),
          sortField: 'amount'
        },
        {
          name: 'invoice_number',
          title: this.$t('payments.invoice'),
          sortField: 'invoice_number',
        },
        {
          name: 'payment_mode',
          title: this.$t('payments.payment_mode'),
          sortField: 'payment_mode',
        },
       this.$t('general.actions')
      ],
      filters: {
        customer: '',
        payment_mode: '',
        payment_number: '',
      },
    }
  },
  created() {
    this.fetchCustomers({limit: 'all'})
    this.fetchData(this.page,this.sortOrder)
  },
  computed: {
    showEmptyScreen() {
      return !this.totalPayments && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('payment', [
      'selectedPayments',
      'totalPayments',
      'payments',
      'selectAllField',
      'paymentModes',
    ]),

    selectField: {
      get: function () {
        return this.selectedPayments
      },
      set: function (val) {
        this.selectPayment(val)
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
    paymentList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },

  mounted() {
    this.fetchPaymentModes({ limit: 'all' })
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllPayments()
    }
  },

  methods: {
    ...mapActions('payment', [
      'fetchPayments',
      'selectAllPayments',
      'selectPayment',
      'deletePayment',
      'deleteMultiplePayments',
      'setSelectAllState',
      'fetchPaymentModes',
    ]),
     ...mapActions('customer', [
      'fetchCustomers'
    ]),
    onTextChange() {
      this.filters.customer = this.customerSelect;
    },
    async searchCustomers(search) {
      this.loading = true

      await this.fetchCustomers({ search })

      this.loading = false
    },
    async fetchData(page,sort) {
      let data = {
        customer_id: this.filters.customer ? this.filters.customer.id: '',
        payment_method_id:
          this.filters.payment_mode !== null
            ? this.filters.payment_mode.id
            : '',
        payment_number: this.filters.payment_number,
        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort && sort[0].direction || 'desc',
        page,
      }
      this.showNomessage = false
      this.isRequestOngoing = true
      
      let response = await this.fetchPayments(data)
      this.isRequestOngoing = false

      this.paymentList = response.data.payments.data
      this.totalCount = response.data.payments.total
      if(response.data.payments.data.length === 0) this.showNomessage = true
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
      let local = this.paymentList;
      
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

    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        customer: '',
        payment_mode: '',
        payment_number: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removePayment(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payments.confirm_delete'),
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePayment({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('payments.deleted_message', 1))
            this.clearFilter()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async removeMultiplePayments() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payments.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let request = await this.deleteMultiplePayments()
          if (request.data.success) {
            window.toastr['success'](this.$tc('payments.deleted_message', 2))
            this.refreshTable()
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      this.refreshTable()
    },

    showModel(selectedRow) {
      this.selectedRow = selectedRow
      this.$refs.Delete_modal.open()
    },

    async removeSelectedItems() {
      this.$refs.Delete_modal.close()
      await this.selectedRow.forEach((row) => {
        this.deletePayment(this.id)
      })
      this.refreshTable()
    },
  },
}
</script>
