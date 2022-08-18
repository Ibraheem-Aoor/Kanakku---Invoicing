<template>
<div>
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
                    <div class="card card-body">
                      <ul class="nav nav-tabs" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">{{$t('settings.customization.invoices.title')}}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">{{$t('settings.customization.estimates.title')}}</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">{{$t('settings.customization.payments.title')}}</a>
                          </li>
                           <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabs-4" role="tab">{{$t('settings.customization.items.title')}}</a>
                          </li>
                        </ul><!-- Tab panes -->
                        <div class="tab-content">
                          <div class="tab-pane active" id="tabs-1" role="tabpanel">
                            <invoices-tab :settings="settings" />
                          </div>
                          <div class="tab-pane" id="tabs-2" role="tabpanel">
                            <estimates-tab :settings="settings" />
                          </div>
                          <div class="tab-pane" id="tabs-3" role="tabpanel">
                            <payments-tab :settings="settings" />
                          </div>
                           <div class="tab-pane" id="tabs-4" role="tabpanel">
                            <items-tab />
                          </div>
                        </div>
                    </div>
                    </div>
                
                  <!-- /Form -->
                  
                </div>
              </div>
            </div>
    </div>
</div>
</template>

<script>
import InvoicesTab from './customization-tabs/InvoicesTab'
import EstimatesTab from './customization-tabs/EstimatesTab'
import PaymentsTab from './customization-tabs/PaymentsTab'
import ItemsTab from './customization-tabs/ItemsTab'
import { mapActions } from 'vuex'

export default {
  data() {
    return {
      settings: {},
      isRequestOnGoing: false,
      tabStatus: 'tab1'
    }
  },

  components: {
    InvoicesTab,
    EstimatesTab,
    PaymentsTab,
    ItemsTab,
  },

  created() {
    this.fetchSettings()
  },

  methods: {
    ...mapActions('company', ['fetchCompanySettings']),
    async fetchSettings() {
      this.isRequestOnGoing = true
      let res = await this.fetchCompanySettings([
        'payment_auto_generate',
        'payment_prefix',
        'payment_mail_body',
        'invoice_auto_generate',
        'invoice_prefix',
        'invoice_mail_body',
        'estimate_auto_generate',
        'estimate_prefix',
        'estimate_mail_body',
        'invoice_billing_address_format',
        'invoice_shipping_address_format',
        'invoice_company_address_format',
        'invoice_mail_body',
        'payment_mail_body',
        'payment_company_address_format',
        'payment_from_customer_address_format',
        'estimate_company_address_format',
        'estimate_billing_address_format',
        'estimate_shipping_address_format',
      ])

      this.settings = res.data
      this.isRequestOnGoing = false
    },
  },
}
</script>
