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
                      <i class="fas fa-edit"></i> <span>{{$t('settings.menu_title.customization')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/preferences" class="nav-link">
                      <i class="fas fa-cog"></i> <span>{{$t('settings.menu_title.preferences')}}</span>
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
                 <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
                <div class="card card-table">
                  <div class="card-header">
                    <div class="row">
                      <div class="col">
                        <h5 class="card-title">{{ $t('settings.tax_types.title') }}</h5>
                      </div>
                      <div class="col-auto">
                        <a  @click="openTaxModal" class="btn btn-outline-primary btn-sm">{{ $t('settings.tax_types.add_new_tax') }}</a>
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
                        <span>{{props.rowData.name}} </span>
                      </template>
                      <template slot="compound_tax" slot-scope="props">
                        <span :style="{ 'background-color': `${$utils.getBadgeStatusColor(props.rowData.compound_tax ? 'YES' : 'NO').bgColor}`, 'color': $utils.getBadgeStatusColor(props.rowData.compound_tax ? 'YES' : 'NO').color }">{{ props.rowData.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}</span>
                      </template>
                      <template :slot="$t('general.actions')" slot-scope="props">
                        <div class="table-button-container">
                              <a  @click="editTax(props.rowData.id)" class="btn btn-sm btn-white text-success mr-2"><i class="far fa-edit mr-1"></i> {{ $t('general.edit') }}</a> 
                              <a  @click="removeTax(props.rowData.id)" class="btn btn-sm btn-white text-danger mr-2"><i class="far fa-trash-alt mr-1"></i>{{ $t('general.delete') }}</a>
                        </div>
                      </template>
                    </vuetable>
                  <vuetable-pagination ref="pagination"
                    @vuetable-pagination:change-page="onChangePage" :css="css.pagination"
                  ></vuetable-pagination>
                  </div>
                   <label class="row form-group toggle-switch mt-4 container-fluid" for="preferencesSwitch1">
                      <span class="col-8 col-sm-9 toggle-switch-content ml-0">
                        <span class="d-block text-dark">{{ $t('settings.tax_types.tax_per_item') }}</span>
                        <span class="d-block text-muted">{{ $t('settings.tax_types.tax_setting_description') }}</span>
                      </span>
                      <span class="col-4 col-sm-3">
                        <input type="checkbox" v-model="formData.tax_per_item" @change="setTax" class="toggle-switch-input" id="preferencesSwitch1">
                        <span class="toggle-switch-label ml-auto">
                          <span class="toggle-switch-indicator"></span>
                        </span>
                      </span>
                    </label>
                </div>
              </div>
                    </div>
                  </form>
                  <!-- /Form -->
                </div>
            </div>
          </div>
          <TaxTypeModal ref="modal" />
    </div>
    </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'
import TaxTypeModal from '../../components/base/modal/TaxTypeModal'
import TableStyle from '../../tablestyle/tablestyle'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
    TaxTypeModal
  },

  data() {
    return {
      formData: {
        tax_per_item: false,
      },
      isRequestOnGoing: false,
      perPage: 5,
      page: 1,
      totalCount: 0,
      taxList: [],
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
        title: this.$tc('settings.tax_types.tax_name'),
        sortField: 'name',
      },
      {
        name: 'percent',
        title: this.$t('settings.tax_types.percent'),
        sortField: 'percent',
        formatter: function(value) {
            return value+"%"
        }
      },
      this.$t('general.actions')
    ],
    }
  },
  watch: {
    filters: {
      handler: _.debounce(function() {
       this.refreshTable();
      }, 500),
      deep: true,
    },
    taxList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
      //document.getElementsByClassName('vuetable-empty-result')[0].style.display = "none";
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },
  computed: {
    ...mapGetters('taxType', ['taxTypes', 'getTaxTypeById']),
  },

  mounted() {
    this.getTaxSetting()
    this.fetchData(1, 'created_at')
  },
  
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('taxType', [
      'indexLoadData',
      'deleteTaxType',
      'fetchTaxType',
      'fetchTaxTypes',
    ]),
    ...mapActions('company', ['fetchCompanySettings', 'updateCompanySettings']),

    async fetchData(page,sort) {
    
      let data = {
        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort &&  sort[0].direction || 'desc',
        page,
      }
      this.isRequestOnGoing = true
      this.showNomessage = false
      let response = await this.fetchTaxTypes(data)
      this.taxList = response.data.taxTypes.data
      this.totalCount = response.data.taxTypes.total
      this.isRequestOnGoing = false
      if(response.data.taxTypes.data.length === 0) this.showNomessage = true
    },

    async getTaxSetting() {
      let response = await this.fetchCompanySettings(['tax_per_item'])
      if (response.data) {
        this.formData.tax_per_item = response.data.tax_per_item === 'YES'
      }
    },

    refreshTable() {
      this.$nextTick(() => {
          this.fetchData(this.page=1,this.sortOrder)
      })
    },

    async setTax(val) {
      let data = {
        settings: {
          tax_per_item: this.formData.tax_per_item ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async removeTax(id, index) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.tax_types.confirm_delete'),
       
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteTaxType(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.tax_types.deleted_message')
            )
            this.fetchData(1,'created_at')
            return true
          }
          window.toastr['error'](this.$t('settings.tax_types.already_in_use'))
        }
      })
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
      let local = this.taxList;
      
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
    openTaxModal() {
      this.$refs.modal.show()
    },

    async editTax(id) {
      let response = await this.fetchTaxType(id)
      this.$refs.modal.edit(response.data.taxType)
    },
  },
}
</script>
