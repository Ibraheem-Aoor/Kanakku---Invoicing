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
                      <h5 class="card-title">{{ $t('settings.customization.payments.payment_modes') }}</h5>
                      <p>{{ $t('settings.customization.payments.description') }}

</p>
                    </div>
                    <div class="col-auto">
                      <a href="javascript:void(0);" @click="addPaymentMode" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#add_category">{{ $t('settings.customization.payments.add_payment_mode') }}</a>
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
                    <template :slot="$t('settings.tax_types.action')" slot-scope="props">
                      <div class="table-button-container">
                            <a @click="editPaymentMode(props.rowData)"  class="btn btn-sm btn-white text-success mr-2"><i class="far fa-edit mr-1"></i> {{ $t('general.edit') }}</a> 
                            <a @click="removePaymentMode(props.rowData.id)" class="btn btn-sm btn-white text-danger mr-2"><i class="far fa-trash-alt mr-1"></i>{{ $t('general.delete') }}</a>
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
          <PaymentModeModal @paymentadded="paymentadded" ref="modal"/>
    </div>
    </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'
import PaymentModeModal from '../../components/base/modal/PaymentModeModal'
import TableStyle from '../../tablestyle/tablestyle'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
    PaymentModeModal
  },
  data() {
    return {
      paymentModes: [],
      isRequestOnGoing: false,
      perPage: 5,
      page: 1,
      totalCount: 0,
      taxList: [],
      css: TableStyle,
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
        title: this.$tc('settings.customization.payments.mode_name'),
        sortField: 'name',
      }, 
      this.$t('settings.tax_types.action')
    ],
    }
  },
  mounted() {
    this.paymentadded()
  },
   watch: {
    paymentModes(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('payment', ['deletePaymentMode', 'fetchPaymentModes']),

    async fetchData(page, sort) {
      let data = {
        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort && sort[0].direction || 'desc',
        page,
      }
      this.isRequestOnGoing = true
      let response = await this.fetchPaymentModes(data)
      this.isRequestOnGoing = false
      this.showNomessage = false
      this.paymentModes = response.data.paymentMethods.data;
      this.totalCount = response.data.paymentMethods.total;
      if(response.data.paymentMethods.total === 0) this.showNomessage = true

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
      let local = this.paymentModes;
      
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
    addPaymentMode() {
      this.$refs.modal.show()
    },
    paymentadded() {
      this.fetchData(this.page,'created_at')
    },
    editPaymentMode(data) {
      this.$refs.modal.edit(data)
    },

    removePaymentMode(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t(
          'settings.customization.payments.payment_mode_confirm_delete'
        ),
        
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deletePaymentMode(id)

          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.payments.deleted_message')
            )
            this.id = null
            this.fetchData(this.page=1, 'created_at')
            return true
          }
          window.toastr['error'](
            this.$t('settings.customization.payments.already_in_use')
          )
        }
      })
    },
  },
}
</script>
