<template>
  <base-page>
          <div class="page-header">
              <div class="row">
                <div class="col-sm-6">
                  <h3 class="page-title">{{ $t('navigation.settings') }}</h3>
                  <ul class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/admin/dashboard">{{ $t('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ $t('navigation.settings') }}</li>
                  </ul>
                </div>
              </div>
            </div>
        
              <div class="row">
                <div class="col-xl-3 col-md-4">
                  
                  <!-- Settings Menu -->
                    <div class="widget settings-menu">
                    <ul>
                      <li class="nav-item">
                        <router-link to="/admin/settings/user-profile" class="nav-link">
                          <i class="far fa-user"></i> <span>{{ $t('settings.account_settings.account_settings') }}</span>
                        </router-link>
                      </li>
                      <li class="nav-item">
                        <router-link to="/admin/settings/company-info" class="nav-link">
                          <i class="far fa-list-alt"></i> <span>{{$t('settings.menu_title.company_information')}}</span>
                        </router-link>
                      </li>
                        <li class="nav-item">
                        <router-link to="/admin/settings/customization" class="nav-link">
                          <i class="fas fa-cog"></i> <span>{{$t('settings.menu_title.customization')}}</span>
                        </router-link>
                      </li>
                      <li class="nav-item">
                        <router-link to="/admin/settings/preferences" class="nav-link">
                          <i class="fas fa-edit"></i> <span>{{$t('settings.menu_title.preferences')}}</span>
                        </router-link>
                      </li>
                      
                        <li class="nav-item">
                        <router-link to="/admin/settings/notifications" class="nav-link">
                          <i class="far fa-bell"></i> <span>{{$t('settings.menu_title.notifications')}}</span>
                        </router-link>
                      </li>
                      <li class="nav-item">
                        <router-link to="/admin/settings/tax-types" class="nav-link">
                          <i class="far fa-check-square"></i> <span>{{$t('settings.menu_title.tax_types')}}</span>
                        </router-link>
                      </li>
                        <li class="nav-item">
                        <router-link to="/admin/settings/payment-mode" class="nav-link">
                          <i class="far fa-credit-card"></i> <span>{{$t('settings.menu_title.payment_modes')}}</span>
                        </router-link>
                      </li>
                        <li class="nav-item">
                        <router-link to="/admin/settings/custom-fields" class="nav-link">
                          <i class="far fa-bell"></i> <span>{{$t('settings.menu_title.custom_fields')}}</span>
                        </router-link>
                      </li>
                      
                      <li class="nav-item">
                        <router-link to="/admin/settings/expense-category" class="nav-link">
                          <i class="far fa-list-alt"></i> <span>{{$t('settings.menu_title.expense_category')}}</span>
                        </router-link>
                      </li>
                      <li class="nav-item">
                        <router-link to="/admin/settings/mail-configuration" class="nav-link">
                          <i class="far fa-check-square"></i> <span>{{$t('settings.mail.mail_config')}}</span>
                        </router-link>
                      </li>
                    </ul>
                  </div>
                  <!-- /Settings Menu -->
                  
                </div>
          
                <div class="col-xl-9 col-md-8 mt-3">
                  <base-loader v-if="isRequestOngoing" :show-bg-overlay="true" />
                    <div class="card card-table">
                      <div class="card-header">
                        <div class="row">
                          <div class="col">
                            <h5 class="card-title">{{ $t('settings.expense_category.title') }}</h5>
                          </div>
                          <div class="col-auto">
                            <a href="javascript:void(0);" @click="addExpenseCategory" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#add_category">{{ $t('settings.expense_category.add_new_category') }}</a>
                          </div>
                        </div>
                      </div>
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
                                  <span>{{props.rowData.name}}</span>
                              </template>
                              <template slot="description" slot-scope="props">
                                  <span>{{props.rowData.description}}</span>
                              </template>
                              <template :slot="$t('settings.tax_types.action')" slot-scope="props">
                                <div class="table-button-container">
                                    <a  @click="editExpenseCategory(props.rowData.id)" class="btn btn-sm btn-white text-success mr-2"><i class="far fa-edit mr-1"></i> {{$t('general.edit')}}</a> 
                                      <a  @click="removeExpenseCategory(props.rowData.id)" class="btn btn-sm btn-white text-danger mr-2"><i class="far fa-trash-alt mr-1"></i>{{$t('general.delete')}}</a>
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
                <!-- /Form -->
              </div>  
            </div>
          </div>
          <CategoryModal ref="modal" />
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'
import CategoryModal from '../../components/base/modal/CategoryModal'
import TableStyle from '../../tablestyle/tablestyle'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
    CategoryModal
  },
  data() {
    return {
      isRequestOngoing: false,
      categoryList: [],
      perPage: 5,
      page: 1,
      totalCount: 0,
      showNomessage: false,
      sortOrder: [
        {
            field: 'created_at',
            sortField: 'created_at',
            direction: 'desc'
        }
      ],
      css: TableStyle,
      showNomessage: false,
      fields: [
      { 
        name: 'name', 
        title: this.$t('settings.expense_category.category_name'),
        sortField: 'name',
      },
      { 
        name: 'description', 
        title: this.$tc('settings.expense_category.category_description'),
        sortField: 'description',
      },
      this.$t('settings.expense_category.action')
    ],
    }
  },
  watch: {
    categoryList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },
  computed: {
    ...mapGetters('category', ['categories', 'getCategoryById']),
  },
  mounted() {
    this.getCategories()
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('category', [
      'fetchCategories',
      'fetchCategory',
      'deleteCategory',
    ]),

    async fetchData(page, sort) {
      let data = {
        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort && sort[0].direction || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchCategories(data)
      this.isRequestOngoing = false
      this.showNomessage = false
      this.categoryList = response.data.categories.data
      this.totalCount = response.data.categories.total
      if(response.data.categories.data.length === 0) this.showNomessage = true
   
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
      let local = this.categoryList;
      
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
    getCategories() {
         this.fetchData(this.page=1,'created_at')
    },
    async removeExpenseCategory(id, index) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.expense_category.confirm_delete'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let response = await this.deleteCategory(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$tc('settings.expense_category.deleted_message')
            )
            this.id = null
            //this.$refs.table.refresh()
            this.getCategories()
            return true
          }
          window.toastr['error'](
            this.$t('settings.expense_category.already_in_use')
          )
        }
      })
    },

    addExpenseCategory() {
      this.$refs.modal.show()
    },

    async editExpenseCategory(id) {
      let response = await this.fetchCategory(id)
      this.$refs.modal.edit(response.data.category)
    },
  },
}
</script>
