<template>
  <base-page>
      <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
          <div class="row align-items-center">
              <div class="col">
                <h3 class="page-title">{{$t('items.title')}}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                      <router-link to="/admin/dashboard">{{$t('general.home')}}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{$t('items.title')}}</li>
                </ul>
              </div>
              <div class="col-auto">
                <router-link  to="items/create" class="btn btn-primary mr-1">
                    <i class="fas fa-plus"></i>
                </router-link>
                <a class="btn btn-primary filter-btn" @click="toggleFilter(1)" v-if="totalItems" id="filter_search">
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
                      <label>{{$t('items.name')}}</label>
                      <input type="text" v-model="filters.name" class="form-control">
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                      <label>{{$tc('items.unit')}}</label>
                      <select class="select form-control" v-model="filters.unit">
                          <option>Select</option>
                          <option v-for="item in itemUnits" :key="item.id" :value="item.id">{{item.name}}</option>
                      </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                      <label>{{$tc('items.price')}}</label>
                      <input type="text" v-model="filters.price" class="form-control">
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
                            <template slot="price" slot-scope="props">
                                <div v-html="$utils.formatMoney(props.rowData.price, defaultCurrency)" />
                            </template>
                            <template slot="name" slot-scope="props">
                                {{props.rowData.name}}
                            </template>
                            <template :slot="$t('items.action')" slot-scope="props">
                            <div class="table-button-container">
                              <router-link :to="`items/${props.rowData.id}/edit`" class="btn btn-sm btn-white text-success mr-2">
                                <i class="fas fa-edit mr-1"></i> {{ $t('general.edit') }}
                              </router-link>
                              <a @click="removeItems(props.rowData.id)" class="btn btn-sm btn-white text-danger">
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
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'
import TableStyle from '../../tablestyle/tablestyle'

export default {
  components: {
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      showNomessage: false,
      filters: {
        name: '',
        unit: '',
        price: '',
      },
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
          name: 'name', 
          title: this.$t('items.name'),
          sortField: 'name'
        }, 
        {
          name: 'unit_name',
          title: this.$t('items.unit'),
          sortField: 'unit_name',
        },
        {
          name: 'price',
          title: this.$t('items.price'),
          sortField: 'price',
        },
        {
          name: 'formattedCreatedAt',
          title: this.$t('items.added_on'),
          sortField: 'created_at',
        },
        this.$t('items.action')
      ],
    }
  },

  computed: {
    ...mapGetters('item', [
      'items',
      'selectedItems',
      'totalItems',
      'selectAllField',
      'itemUnits',
    ]),

    ...mapGetters('company', ['defaultCurrency']),

    showEmptyScreen() {
      return !this.totalItems && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    selectField: {
      get: function () {
        return this.selectedItems
      },
      set: function (val) {
        this.selectItem(val)
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
    itemsList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },

  mounted() {
    this.fetchItemUnits({ limit: 'all' })
    this.fetchData(this.page, this.sortOrder)

  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllItems()
    }
  },

  methods: {
    ...mapActions('item', [
      'fetchItems',
      'selectAllItems',
      'selectItem',
      'deleteItem',
      'deleteMultipleItems',
      'setSelectAllState',
      'fetchItemUnits',
    ]),
    refreshTable() {
      this.fetchData(this.page, this.sortOrder);
    },

    async fetchData(page, sort) {
     
      let data = {
        search: this.filters.name !== null ? this.filters.name : '',
        unit_id: this.filters.unit !== null ? this.filters.unit : '',
        price: Math.round(this.filters.price * 100),
        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort && sort[0].direction || 'desc',
        page,
      }
      this.showNomessage = false
      this.isRequestOngoing = true
      let response = await this.fetchItems(data)
      this.itemsList = response.data.items.data
      this.totalCount = response.data.items.total
      if(response.data.items.data.length === 0) this.showNomessage = true
      this.isRequestOngoing = false

    },

    clearFilter() {
      this.filters = {
        name: '',
        unit: '',
        price: '',
      }
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
      let local = this.itemsList;
      
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
    async removeItems(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteItem({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('items.deleted_message', 1))
            this.refreshTable()
            return true
          }

          if (res.data.error === 'item_attached') {
            window.toastr['error'](
              this.$tc('items.item_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async removeMultipleItems() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete', 2),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteMultipleItems()

          if (res.data.success || res.data.items) {
            window.toastr['success'](this.$tc('items.deleted_message', 2))
            this.$refs.table.refresh()
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
  },
}
</script>
