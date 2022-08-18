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
                      <i class="far fa-credit-card"></i> <span>{{$t('settings.menu_title.notifications')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/tax-types" class="nav-link">
                      <i class="far fa-check-square"></i> <span>{{$t('settings.menu_title.tax_types')}}</span>
                    </router-link>
                  </li>
                   <li class="nav-item">
                    <router-link to="/admin/settings/payment-mode" class="nav-link">
                      <i class="far fa-bell"></i> <span>{{$t('settings.menu_title.payment_modes')}}</span>
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
                      <h5 class="card-title">{{ $t('settings.menu_title.custom_fields') }}</h5>
                      <p>{{ $t('settings.custom_fields.section_description') }}</p>
                    </div>
                    <div class="col-auto">
                      <a @click="addCustomField" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#add_category">{{ $t('settings.custom_fields.add_custom_field') }}</a>
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
                       <template slot="label" slot-scope="props">
                          <span>{{props.rowData.label}}</span>
                      </template>
                       <template slot="is_required" slot-scope="props">
                          <span  :style="{ 'background-color': `${$utils.getBadgeStatusColor(props.rowData.is_required ? 'YES' : 'NO').bgColor}`, 'color': `${$utils.getBadgeStatusColor(props.rowData.is_required ? 'YES' : 'NO').color}` }"> {{
                                props.rowData.is_required
                                  ? $t('settings.custom_fields.yes')
                                  : $t('settings.custom_fields.no').replace('_', ' ')
                          }}</span>
                    </template>
                    <template :slot="$t('settings.tax_types.action')" slot-scope="props">
                      <div class="table-button-container">
                            <a  @click="editCustomField(props.rowData.id)" class="btn btn-sm btn-white text-success mr-2"><i class="far fa-edit mr-1"></i> {{$t('general.edit')}}</a> 
                            <a  @click="removeCustomField(props.rowData.id)" class="btn btn-sm btn-white text-danger mr-2"><i class="far fa-trash-alt mr-1"></i>{{$t('general.delete')}}</a>
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
                  
                    
                  </form>
                  <!-- /Form -->
                  
                </div>
              </div>
            </div>
            <CustomFields @customfieldsadded="customfieldsadded" ref="modal"/>
    </div>
    </base-page>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { PencilIcon, TrashIcon, PlusIcon } from '@vue-hero-icons/solid'
import CustomFields from '../../components/base/modal/CustomField/Index'
import TableStyle from '../../tablestyle/tablestyle'
export default {
  components: {
    PencilIcon,
    TrashIcon,
    PlusIcon,
    CustomFields
  },
  mounted() {
    this.customeFieldsList()
  },
  data() {
    return {
      customFieldsList: [],
      isRequestOnGoing: false,
      perPage: 5,
      page: 1,
      totalCount: 0,
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
        title: this.$tc('settings.custom_fields.name'),
        sortField: 'name',
      },
      { 
        name: 'label', 
        title: this.$tc('settings.custom_fields.label'),
        sortField: 'label',
      },
      { 
        name: 'model_type', 
        title: this.$tc('settings.custom_fields.model'),
        sortField: 'model_type',
      },
      { 
        name: 'type', 
        title: this.$tc('settings.custom_fields.type'),
        sortField: 'type',
      },
      { 
        name: 'is_required', 
        title: this.$tc('settings.custom_fields.required'),
        sortField: 'is_required',
      },
      this.$t('settings.tax_types.action')
    ],
    }
  },
  watch: {
    customFieldsList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },
  methods: {
    ...mapActions('customFields', ['fetchCustomFields', 'deleteCustomFields']),

    ...mapActions('modal', ['openModal']),

    async fetchData(page, sort) {
      let data = {
        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort && sort[0].direction || 'desc',
        page,
      }
      this.isRequestOnGoing = true
      let response = await this.fetchCustomFields(data)
      this.isRequestOnGoing = false
      this.showNomessage = false
      this.customFieldsList = response.data.customFields.data;
      this.totalCount = response.data.customFields.total;
      if(this.customFieldsList.length === 0) this.showNomessage = true
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
      let local = this.customFieldsList;
      
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
    customeFieldsList() {
        this.fetchData(this.page=1, 'created_at')
    },
    customfieldsadded() {
        this.customeFieldsList()
    },
    async removeCustomField(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.custom_fields.custom_field_confirm_delete'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteCustomFields(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.custom_fields.deleted_message')
            )
            this.id = null
            //this.$refs.table.refresh()
            this.customeFieldsList()
            return true
          }
          window.toastr['error'](
            this.$t('settings.custom_fields.already_in_use')
          )
        }
      })
    },

    addCustomField() {
      this.$refs.modal.show()
    },

    editCustomField(id) {
      this.$refs.modal.edit(id)
    },
  },
}
</script>
