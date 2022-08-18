<template>
      <base-page v-if="isSuperAdmin" class="items">
        <div class="page-header">
           <div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">{{$t('users.title')}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link>
									</li>
									<li class="breadcrumb-item active">{{$t('users.title',2)}}</li>
								</ul>
							</div>
							<div class="col-auto">
								<router-link  to="users/create" class="btn btn-primary mr-1">
									<i class="fas fa-plus"></i>
								</router-link>
								<a class="btn btn-primary filter-btn" v-if="totalUsers" @click="toggleFilter(1)" id="filter_search">
									<i class="fas fa-filter"></i>
								</a>
							</div>
						</div>
          </div>
          	<!-- Search Filter -->
					<div class="card filter-card"  v-show="showFilters">
						<div class="card-body pb-0">
							<div class="row">
								<div class="col-sm-6 col-md-3">
									<div class="form-group">
										<label>{{$t('users.name')}}</label>
										<input type="text" v-model="filters.name" class="form-control">
									</div>
								</div>
								<div class="col-sm-6 col-md-3">
									<div class="form-group">
										<label>{{$t('users.email')}}</label>
										<input type="text" v-model="filters.email" class="form-control">
									</div>
								</div>
								<div class="col-sm-6 col-md-3">
									<div class="form-group">
										<label>{{$t('users.phone')}}</label>
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
                  <template slot="name" slot-scope="props">
                        {{props.rowData.name}}
                    </template>
                  <template :slot="$t('users.action')" slot-scope="props">
                    <div class="table-button-container">
                        	<router-link class="btn btn-sm btn-white text-success mr-2" :to="`users/${props.rowData.id}/edit`"><i class="far fa-edit mr-1"></i> {{ $t('general.edit') }}</router-link> 
                          <a @click="removeUser(props.rowData.id)" class="btn btn-sm btn-white text-danger mr-2"><i class="far fa-trash-alt mr-1"></i> {{ $t('general.delete') }}</a> 
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
          </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import CssForBootstrap4 from '../../tablestyle/tablestyle'

export default {

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      perPage: 10,
      page: 1,
      showNomessage: false,
      sortOrder: [
      {
          field: 'created_at',
          sortField: 'created_at',
          direction: 'desc'
      }
    ],
      totalCount: 0,
      usersList: [],
      css: CssForBootstrap4,
      filters: {
        name: '',
        email: '',
        phone: '',
      },
      fields: [
      { 
        name: 'name', 
        title: this.$t('users.name'),
        sortField: 'name'
      }, 
      {
        name: 'email',
        title: this.$t('users.email'),
        sortField: 'email'
      },
      {
        name: 'phone',
        title: this.$t('users.phone'),
        sortField: 'phone'
      },
       {
        name: 'formattedCreatedAt',
        title: this.$t('users.added_on'),
        sortField: 'created_at'
      },
      this.$t('users.action')
    ],
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('users', [
      'users',
      'selectedUsers',
      'totalUsers',
      'selectAllField',
    ]),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.totalUsers && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    selectField: {
      get: function () {
        return this.selectedUsers
      },
      set: function (val) {
        this.selectedUser(val)
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
  
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    this.fetchData(this.page,this.sortOrder);
  },
  watch: {
    filters: {
      handler: _.debounce(function() {
       this.refreshTable();
      }, 500),
      deep: true,
    },
    usersList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
      //document.getElementsByClassName('vuetable-empty-result')[0].style.display = "none";
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },
  destroyed() {
    if (this.selectAllField) {
      this.selectAllUsers()
    }
  },
  
  methods: {
    ...mapActions('users', [
      'fetchUsers',
      'selectAllUsers',
      'selectedUser',
      'deleteUser',
      'deleteMultipleUsers',
      'setSelectAllState',
    ]),

    refreshTable() {
        this.$nextTick(() => {
           this.fetchData(this.page=1,this.sortOrder)
        })
    },
    async fetchData(page,sort) {
     // this.usersList = [];
      this.showNomessage = false;
      let data = {
        display_name: this.filters.name !== null ? this.filters.name : '',
        phone: this.filters.phone !== null ? this.filters.phone : '',
        email: this.filters.email !== null ? this.filters.email : '',

        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort && sort[0].direction || 'desc',
        page,
      }

      this.isRequestOngoing = true

      let response = await this.fetchUsers(data)
      
      this.usersList = response.data.users.data;
      if(response.data.users.data.length === 0) this.showNomessage = true;
      this.totalCount = response.data.users.total;
      
      this.isRequestOngoing = false
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
      if (this.usersList.length < 1) console.log("");
      let local = this.usersList;
      
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
     
    // console.log(_.slice(local, from, to)+"pp"+local+"SSS"+from+"oo"+to);
      return {
        pagination: pagination,
        data: local
      };
    },
    
    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        name: '',
        email: '',
        phone: '',
      }
    },

    toggleFilter(value) {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removeUser(id) {
      let user = []
      user.push(id)

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('users.confirm_delete'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteUser(user)

          if (res.data.success) {
            window.toastr['success'](this.$tc('users.deleted_message', 1))
            this.clearFilter();
            return true
          }

          if (res.data.error === 'user_attached') {
            window.toastr['error'](
              this.$tc('users.user_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async removeMultipleUsers() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('users.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteMultipleUsers()

          if (res.data.success || res.data.users) {
            window.toastr['success'](this.$tc('users.deleted_message', 2))
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
