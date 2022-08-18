<template>
        <div class="content container-fluid">
					<!-- Page Header -->
            <div class="page-header">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="page-title">{{$t('customers.title')}}</h3>
                  <ul class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{$t('customers.title')}}</li>
                  </ul>
                </div>
                <div class="col-auto">
                  <router-link  to="customers/create" class="btn btn-primary mr-1">
                    <i class="fas fa-plus"></i>
                  </router-link>
                  <a class="btn btn-primary filter-btn" @click="toggleFilter(1)" v-if="totalCustomers" id="filter_search">
                    <i class="fas fa-filter"></i>
                  </a>
                </div>
              </div>
            </div>
            <!-- /Page Header -->
              
            <!-- Search Filter -->
            <div class="card filter-card"  v-show="showFilters">
              <div class="card-body pb-0">
                <div class="row">
                  <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                      <label>{{$t('customers.display_name')}}</label>
                      <input type="text" v-model="filters.display_name" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                      <label>{{$t('customers.contact_name')}}</label>
                      <input type="text" v-model="filters.contact_name" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                      <label>{{$t('customers.phone')}}</label>
                      <input type="text" v-model="filters.phone" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
              <div>  <label
            class="absolute text-sm leading-snug text-black cursor-pointer text-danger"
            style="top: 10px;right:15px;position:absolute"
            @click="clearFilter"
            >{{ $t('general.clear_all') }}</label></div>
            </div>
            <!-- /Search Filter -->
            
            <div class="row">
              <base-loader v-if="isRequestOngoing" :show-bg-overlay="true" />
              <div class="col-sm-12">
                <div class="custom_Card">
                <div class="card card-table bg-transaparent">
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
                      <template slot="name" slot-scope="props">
                          <router-link  :to="{ path: `customers/${props.rowData.id}/view` }" class="avatar avatar-sm mr-2"><img class="avatar-img rounded-circle" :src="$utils.userImage(props.rowData.avatar)" @error="altImg" alt="User Image"></router-link>
                          <router-link  :to="{ path: `customers/${props.rowData.id}/view` }">{{props.rowData.name}}</router-link>
                      </template>
                      <template slot="contact_name" slot-scope="props">
                         <span>{{props.rowData.contact_name ? props.rowData.contact_name : "No Contact Name"}}</span>
                      </template>
                      <template slot="phone" slot-scope="props">
                         <span>{{props.rowData.phone ? props.rowData.phone : "No Contact Number"}}</span>
                      </template>
                    <template :slot="$t('customers.action')" slot-scope="props">
                      <div class="table-button-container">
                            <router-link class="btn btn-sm btn-white text-success mr-2" :to="`customers/${props.rowData.id}/edit`"><i class="far fa-edit mr-1"></i> {{ $t('general.edit') }}</router-link> 
                            <router-link class="btn btn-sm btn-white text-info mr-2" :to="`customers/${props.rowData.id}/view`"><i class="far fa-edit mr-1"></i> {{ $t('general.view') }}</router-link> 
                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2" @click="removeCustomer(props.rowData.id)"><i class="far fa-trash-alt mr-1"></i>{{ $t('general.delete') }}</a>
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
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { PlusSmIcon } from '@vue-hero-icons/solid'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
} from '@vue-hero-icons/solid'
import TableStyle from '../../tablestyle/tablestyle'

export default {
  components: {
    ChevronDownIcon,
    PlusSmIcon,
    FilterIcon,
    XIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      showNomessage: false,
      filters: {
        display_name: '',
        contact_name: '',
        phone: '',
      },
      customersList: [],
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
      fields: [
        { 
          title: this.$t('customers.display_name'),
          sortField: 'name',
          name: 'name'
        }, 
        {
          name: 'contact_name',
          title: this.$t('customers.contact_name'),
          sortField: 'contact_name',
        },
        {
          name: 'phone',
          title: this.$t('customers.phone'),
          sortField: 'phone',
        },
        {
          title: this.$t('customers.amount_due'),
          sortField: 'due_amount',
          formatter: (value) => {
              return this.$utils.formatMoney(value.due_amount, value.currency)
          }
        },
         {
          name: 'formattedCreatedAt',
          title: this.$t('customers.added_on'),
          sortField: 'created_at',
        },
        this.$t('customers.action')
      ],
    }
  },  
  computed: {
    showEmptyScreen() {
      return !this.totalCustomers && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('customer', [
      'customers',
      'selectedCustomers',
      'totalCustomers',
      'selectAllField',
    ]),
    selectField: {
      get: function () {
        return this.selectedCustomers
      },
      set: function (val) {
        this.selectCustomer(val)
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
    customersList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },
  
  destroyed() {
    if (this.selectAllField) {
      this.selectAllCustomers()
    }
  },
  mounted() {
    this.fetchData(this.page, this.sortOrder)
  },
  methods: {
    ...mapActions('customer', [
      'fetchCustomers',
      'selectAllCustomers',
      'selectCustomer',
      'deleteCustomer',
      'deleteMultipleCustomers',
      'setSelectAllState',
    ]),
    async fetchData(page,sort) {
      let data = {
        display_name: this.filters.display_name,
        contact_name: this.filters.contact_name,
        phone: this.filters.phone,
        orderByField: sort && sort.fieldName || 'created_at',
        orderBy: sort && sort.order || 'desc',
        page,
      }
      this.showNomessage = false;
      this.isRequestOngoing = true
      let response = await this.fetchCustomers(data)
	    this.customersList = response.data.customers.data
      this.totalCount =  response.data.customers.total
      this.isRequestOngoing = false
      if(response.data.customers.data.length === 0)  this.showNomessage = true
    },
    clearFilter() {
      this.filters = {
        display_name: '',
        contact_name: '',
        phone: '',
      }
    },
    altImg(e) {
      e.target.src = '/assets/img/default-avatar.jpg'
    },
    toggleFilter(value) {
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
      let local = this.customersList;
      
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
    async removeCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteCustomer({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('customers.deleted_message', 1))
            this.refreshTable()
            return true
          }

          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async removeMultipleCustomers() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete', 2),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let request = await this.deleteMultipleCustomers()
          if (request.data.success) {
            window.toastr['success'](this.$tc('customers.deleted_message', 2))
            this.refreshTable()
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    },
  },
}
</script>
