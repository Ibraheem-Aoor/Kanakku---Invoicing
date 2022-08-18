<template>
  <base-page>
        <div class="content container-fluid">
					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col"> 
								<h3 class="page-title">{{$t('expenses.title')}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link></li>
									<li class="breadcrumb-item active">{{$t('expenses.title')}}</li>
								</ul>
							</div>
							<div class="col-auto">
								<router-link to="/admin/expenses/create"  class="btn btn-primary">
									<i class="fas fa-plus"></i>
								</router-link>
								<a class="btn btn-primary filter-btn" v-if="totalExpenses" href="javascript:void(0);" @click="toggleFilter(1)" id="filter_search">
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
										<label>{{$t('expenses.customer')}}</label>
										<select class="select form-control" v-model="filters.user">
											<option>{{'Select Customer'}}</option>
                      <option v-for="item in customers" :key="item.id" :value="item.id">{{item.name}}</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>{{$t('expenses.category')}}</label>
										<select class="select form-control" v-model="filters.category">
											<option>{{$t('expenses.categories.select_a_category')}}</option>
                      <option v-for="item in categories" :key="item.id" :value="item.id">{{item.name}}</option>
										</select>
									</div>
								</div>
                
								<div class="col-md-3">
									<div class="form-group">
										<label>{{$t('expenses.from_date')}}</label>
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
										<label>{{$t('expenses.to_date')}}</label>
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
                  <template slot="user_name" slot-scope="props">
                    <router-link  :to="{ path: `customers/${props.rowData.user_id}/view` }" class="avatar avatar-sm mr-2"><img class="avatar-img rounded-circle" :src="$utils.userImage(props.rowData.user.avatar)" @error="altImg" alt="User Image"></router-link>
                    <router-link  :to="{ path: `customers/${props.rowData.user_id}/view` }">{{props.rowData.user_name}}</router-link>
                  </template>
                  <template slot="notes" slot-scope="props">
                      {{props.rowData.notes ? props.rowData.notes : "-"}}
                  </template>
                     <template slot="amount" slot-scope="props">
                    <div v-html="$utils.formatMoney(props.rowData.amount,defaultCurrency)" />
                  </template>
                  <template :slot="$t('general.actions')" slot-scope="props">
                    <div class="table-button-container">
                        	<router-link class="btn btn-sm btn-white text-success mr-2" :to="`expenses/${props.rowData.id}/edit`"><i class="far fa-edit mr-1"></i> {{ $t('general.edit') }}</router-link> 
                          <a @click="removeExpense(props.rowData.id)" class="btn btn-sm btn-white text-danger mr-2"><i class="far fa-trash-alt mr-1"></i> {{ $t('general.delete') }}</a> 
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
import CssForBootstrap4 from '../../tablestyle/tablestyle'

import {
  PencilIcon,
  TrashIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      perPage: 10,
      page: 1,
      showNomessage: false,
      expenseList: [],
      totalCount: 0,
      sortOrder: [
        {
            field: 'created_at',
            sortField: 'created_at',
            direction: 'desc'
        }
      ],
      css: CssForBootstrap4,
      fields: [
      { 
        name: 'name', 
        title: this.$tc('expenses.categories.category', 1),
        sortField: 'name',
        formatter: (value) => {
            return value || '-'
        }
      }, 
      {
        name: 'user_name',
        title: this.$tc('expenses.customer'),
        sortField: 'user_name',
        formatter: (value) => {
            return value || '-'
        }
      },
      {
        name: 'formattedExpenseDate',
        title: this.$t('expenses.date'),
        sortField: 'expense_date'
      },
      {
        name: 'notes',
        title: this.$t('expenses.note'),
        sortField: 'notes',
      },
       {
        name: 'amount',
        title: this.$t('expenses.amount'),
        sortField: 'amount',
      },
      this.$t('general.actions')
    ],
      filters: {
        category: null,
        from_date: '',
        to_date: '',
        user: '',
      },
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalExpenses && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('category', ['categories']),

    ...mapGetters('expense', [
      'selectedExpenses',
      'totalExpenses',
      'expenses',
      'selectAllField',
    ]),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('customer', ['customers']),

    selectField: {
      get: function () {
        return this.selectedExpenses
      },
      set: function (val) {
        this.selectExpense(val)
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
    expenseList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
      //document.getElementsByClassName('vuetable-empty-result')[0].style.display = "none";
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllExpenses()
    }
  },

  created() {
    this.fetchCategories({ limit: 'all' })
    this.fetchCustomers({ limit: 'all' })
    this.fetchData(this.page, this.sortOrder)
  },
  methods: {
    ...mapActions('expense', [
      'fetchExpenses',
      'selectExpense',
      'deleteExpense',
      'deleteMultipleExpenses',
      'selectAllExpenses',
      'setSelectAllState',
    ]),

    ...mapActions('category', ['fetchCategories']),

    ...mapActions('customer', ['fetchCustomers']),

    async fetchData(page,sort) {
      let data = {
        user_id: this.filters.user ? this.filters.user : null,
        expense_category_id:
          this.filters.category !== null ? this.filters.category : '',

        from_date:
          this.filters.from_date === ''
            ? this.filters.from_date
            : this.filters.from_date,

        to_date:
          this.filters.to_date === ''
            ? this.filters.to_date
            : this.filters.to_date,

        orderByField: sort && sort[0].sortField || 'created_at',

        orderBy: sort &&  sort[0].direction || 'desc',

        page,
      }
      this.showNomessage = false
      this.isRequestOngoing = true
      let response = await this.fetchExpenses(data)
      this.expenseList = response.data.expenses.data
      this.totalCount = response.data.expenses.total

      if(response.data.expenses.total === 0) this.showNomessage = true
      this.isRequestOngoing = false

    },
    onPaginationData (paginationData) {
     this.$refs.pagination.setPaginationData(paginationData)
    },
    altImg(e) {
      e.target.src = '/assets/img/default-avatar.jpg'
    },
    async onChangePage (page) {
      this.page = page;
      await this.fetchData(page,this.sortOrder);
      this.$refs.vuetable.changePage(page)
    },
    dataManager(sortOrder, pagination) {
      let local = this.expenseList;
      
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

    clearFilter() {
      this.filters = {
        category: null,
        from_date: '',
        to_date: '',
        user: null,
      }
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.user = ''
      this.refreshTable()
    },

    toggleFilter(value) {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removeExpense(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete'),
       
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteExpense({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 1))
            //this.refreshTable()
           this.clearFilter();
            return true
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async removeMultipleExpenses() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete', 2),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let request = await this.deleteMultipleExpenses()

          if (request.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 2))
            this.clearFilter();
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    },
  },
}
</script>
