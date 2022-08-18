<template>
  <base-page class="relative payment-create">
      <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
                
							<div class="col-sm-12">
								<h3 class="page-title">{{$tc('payments.title')}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link></li>
									<li class="breadcrumb-item"><router-link to="/admin/payments">{{$tc('payments.payment', 2)}}</router-link></li>
									<li class="breadcrumb-item active" v-if="$route.name === 'payments.edit'">{{$t('payments.edit_payment')}}</li>
                  <li class="breadcrumb-item active" v-else>{{$t('payments.new_payment')}}</li>

								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<div class="row">
						<div class="col-md-12">
                <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
							<div class="card"> 
								<div class="card-body">  
									<form @submit.prevent="submitPaymentData">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>{{$t('payments.date')}}<span class="text-danger">*</span></label>
													<div class="datemx">
													  
                    
                              <datepicker v-model="formData.payment_date"
                                  :disabled-date="$utils.disabledAfterToday" :editable="false" :clearable="false" :class="{ 'is-invalid': $v.formData.payment_date.$error }" @input="$v.formData.payment_date.$touch()"
                                  value-type="format" format="YYYY-MM-DD">
                              </datepicker>
                              <div class="invalid-feedback" v-if="$v.formData.payment_date.$error">{{DateError}}</div>
										       
													</div>
												</div>
												<div class="form-group">
													<label>{{$t('payments.customer')}}<span class="text-danger">*</span></label>
													<select class="select form-control" :class="{ 'is-invalid': $v.selectCustomer.$error }" @change="$v.selectCustomer.$touch()" v-model="selectCustomer" :disabled="isEdit">
														<option>{{$t('customers.select_a_customer')}}</option>
														<option v-for="item in customers" :key="item.id" :value="item.id">{{item.name}}</option>
													</select>
                          <div class="invalid-feedback" v-if="$v.selectCustomer.$error">{{customerError}}</div>
												</div>
                        	<div class="form-group">
													<label>{{$t('payments.payment_mode')}}</label>
													<select class="select form-control" v-model="selectPaymentmethod">
														<option>{{$t('payments.select_payment_mode')}}</option>
                            <option v-for="item in paymentModes" :key="item.id" :value="item.id">{{item.name}}</option>
													</select>
												</div>
											</div>
											<div class="col-md-6"> 
												<div class="form-group">
													<label>{{$t('payments.amount')}}<span class="text-danger">*</span></label>
													<input type="text"  v-model="amount" v-money="money" :class="{ 'is-invalid': $v.formData.amount.$error }" @input="$v.formData.amount.$touch()" class="form-control">
                          <div class="invalid-feedback" v-if="$v.formData.amount.$error">{{amountError}}</div>
                        </div> 
												<div class="form-group">
													<label>{{$t('payments.payment_number')}}<span class="text-danger">*</span></label>
													<input type="text" v-model.trim="paymentNumAttribute" :class="{ 'is-invalid': $v.paymentNumAttribute.$error }"  @input="$v.paymentNumAttribute.$touch()"  class="form-control">
                          <div class="invalid-feedback">{{paymentNumError}}</div>
                        </div> 
												<div class="form-group">
													<label>{{$t('payments.invoice')}}</label>
													<select v-if="!isEdit" class="select form-control" :disabled="!selectCustomer || !invoiceList.length" :placeholder="$t('invoices.select_invoice')" v-model="formData.invoice_id" @change="invoicechange">
														<option v-for="item in invoiceList" :key="item.id" :value="item.id">{{item.invoice_number}}(<div v-html="$utils.formatMoney(item.due_amount, item.user.currency)" />)</option> 
													</select>
                          
                          <select v-else-if="isEdit && invoice" class="select form-control" :disabled="true" :placeholder="$t('invoices.select_invoice')" v-model="formData.invoice_id">
														<option  :value="invoice.id">{{invoice.invoice_number}}(<div v-html="$utils.formatMoney(invoice.due_amount, customer && customer.currency && customer.currency || defaultCurrencyForInput)" />)</option> 
													</select>
                          <select v-else class="select form-control" :disabled="!selectCustomer || !invoiceList.length" :placeholder="$t('invoices.select_invoice')" @change="invoicechange" v-model="formData.invoice_id">
														<option v-for="item in invoiceList" :key="item.id" :value="item.id">{{item.invoice_number}}(<div v-html="$utils.formatMoney(item.due_amount, item.user.currency)" />)</option> 
													</select>
												</div> 
											</div> 
                      <div class="col-md-12"> 
                        <div class="form-group">
                          
                          <vue-editor v-model="formData.notes" id="editor1"  />
                        </div>
												<div class="text-right mt-4">
                           <base-custom-input
                              v-model="formData.notes"
                              :fields="PaymentFields"
                              class="mb-4"
                            />
													<button type="submit" :disabled="isLoading" class="btn btn-primary"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                            {{
                              isEdit ? $t('payments.update_payment') : $t('payments.save_payment')
                            }}
                          </button>
												</div>
											</div> 
										</div>
									</form> 
								</div>
							</div>
						</div>
					</div>
				</div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import { ShoppingCartIcon } from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'
const { required, between, numeric } = require('vuelidate/lib/validators')
import { VueEditor } from "vue2-editor";

export default {
  components: { ShoppingCartIcon, VueEditor},
  mixins: [CustomFieldsMixin],

  data() {
    return {
      amount: 100,
      formData: {
        user_id: null,
        payment_number: null,
        payment_date: this.$utils.currentDate(),
        amount: 100,
        payment_method: null,
        invoice_id: null,
        notes: null,
        payment_method_id: null,
      },
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
      editorData: '<p>Content of the editor.</p>',
      customer: null,
      selectCustomer: null,
      selectPaymentmethod: null,
      invoice: null,
      invoiceList: [],
      isLoading: false,
      isRequestOnGoing: false,
      maxPayableAmount: Number.MAX_SAFE_INTEGER,
      isSettingInitialData: true,
      paymentNumAttribute: null,
      paymentPrefix: '',
      PaymentFields: [
        'customer',
        'company',
        'customerCustom',
        'payment',
        'paymentCustom',
      ],
      content: '<h1>Text your field</h1>'
    }
  },
  validations() {
    return {
      selectCustomer: {
        required,
      },
      formData: {
        payment_date: {
          required,
        },
        amount: {
          required,
          between: between(1, this.maxPayableAmount + 1),
        },
      },
      paymentNumAttribute: {
        required,
        numeric,
      },
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    ...mapGetters('payment', ['paymentModes', 'selectedNote']),
    ...mapGetters('customer', ['customers']),
    pageTitle() {
      if (this.$route.name === 'payments.edit') {
        return this.$t('payments.edit_payment')
      }
      return this.$t('payments.new_payment')
    },
    isEdit() {
      if (this.$route.name === 'payments.edit') {
        return true
      }
      return false
    },
    // amount: {
    //   get: function () {
    //     return this.formData.amount / 100
    //   },
    //   set: function (newValue) {
    //     this.formData.amount = Math.round(newValue * 100)
    //   },
    // },
    customerCurrency() {
      if (this.customer && this.customer.currency) {
        return {
          decimal: this.customer.currency.decimal_separator,
          thousands: this.customer.currency.thousand_separator,
          prefix: this.customer.currency.symbol + ' ',
          precision: this.customer.currency.precision,
          masked: false,
        }
      } else {
        return this.defaultCurrencyForInput
      }
    },
    customerError() {
      if (!this.$v.selectCustomer.$error) {
        return ''
      }

      if (!this.$v.selectCustomer.required) {
        return this.$tc('validation.required')
      }
    },
    DateError() {
      if (!this.$v.formData.payment_date.$error) {
        return ''
      }
      if (!this.$v.formData.payment_date.required) {
        return this.$t('validation.required')
      }
    },
    amountError() {
      if (!this.$v.formData.amount.$error) {
        return ''
      }

      if (!this.$v.formData.amount.required) {
        return this.$t('validation.required')
      }

      if (
        !this.$v.formData.amount.between &&
        this.$v.formData.amount.numeric &&
        this.amount <= 0
      ) {
        
        return this.$t('validation.payment_greater_than_zero')
      }

      if (!this.$v.formData.amount.between && this.amount > 0) {
        return this.$t('validation.payment_greater_than_due_amount')
      }
    },
    paymentNumError() {
      if (!this.$v.paymentNumAttribute.$error) {
        return ''
      }

      if (!this.$v.paymentNumAttribute.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.paymentNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
  },
  watch: {
    selectCustomer(newValue) {
      this.formData.user_id = newValue
      this.customer = this.customers.find(item => item.id === newValue)
      this.money.prefix = this.customer && this.customer.currency && this.customer.currency.symbol+' ' || '$'
      if (!this.isEdit) {
        if (this.isSettingInitialData) {
          this.isSettingInitialData = false
        } else {
          this.invoice = null
          this.formData.invoice_id = null
        }
        this.amount = 0.00
        this.invoiceList = []
        this.fetchCustomerInvoices(newValue)
      }
    },
    amount(newValue) {
        if(newValue && !_.isNaN(newValue) ){
          if(newValue.toString().includes(this.money.prefix)) {
            newValue = newValue.replace(this.money.prefix,'');
          } if(newValue.toString().includes(",")) {
              newValue = newValue.replace(/\,/g,'');
          } else {
              newValue = newValue
          }
          
          if (parseFloat(newValue) > 0) {
              this.formData.amount = Math.round(newValue * 100)
          } else {
              this.formData.amount = newValue
          }
        }
    },
    selectPaymentmethod(newValue) {
      this.formData.payment_method = this.paymentModes.find(item => item.id === newValue);
    },
    selectedNote() {
      if (this.selectedNote) {
        this.formData.notes = this.selectedNote
      }
    },
    invoice_id(newValue) {
      if (newValue) {
        this.formData.invoice_id = newValue
        this.invoice = this.invoices.find(item => item.id === newValue)
        if (!this.isEdit) {
          this.setPaymentAmountByInvoiceData(newValue)
        }
      }
    },
  },
  created() {
    this.money.prefix = this.defaultCurrencyForInput && this.defaultCurrencyForInput.prefix
  },
  async mounted() {
    this.$v.formData.$reset()
    this.resetSelectedNote()
    this.$nextTick(() => {
      this.loadData()
      if (this.$route.params.id && !this.isEdit) {
        this.setInvoicePaymentData()
      }
    })
    
  },
  methods: {
    ...mapActions('invoice', ['fetchInvoice', 'fetchInvoices']),

    ...mapActions('payment', [
      'addPayment',
      'updatePayment',
      'fetchPayment',
      'fetchPaymentModes',
      'resetSelectedNote',
    ]),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('customer', ['fetchCustomers']),

    invoiceWithAmount({ invoice_number, due_amount }) {
      return `${invoice_number} (${this.$utils.formatGraphMoney(
        due_amount,
        this.customer.currency
      )})`
    },
    invoicechange() {
       if (this.formData.invoice_id) {
        this.invoice = this.invoiceList.find(item => item.id === this.formData.invoice_id)
        if (!this.isEdit) {
          this.setPaymentAmountByInvoiceData(this.formData.invoice_id)
        }
      }
    },
    async addPaymentMode() {
      this.openModal({
        title: this.$t('settings.customization.payments.add_payment_mode'),
        componentName: 'PaymentMode',
      })
    },

    async checkAutoGenerate() {
      let response = await this.fetchCompanySettings(['payment_auto_generate'])

      let response1 = await axios.get('/api/v1/next-number?key=payment')

      if (response.data && response.data.payment_auto_generate === 'YES') {
        if (response1.data) {
          this.paymentNumAttribute = response1.data.nextNumber
          this.paymentPrefix = response1.data.prefix
          return true
        }
      } else {
        this.paymentPrefix = response1.data.prefix
      }
    },

    async loadData() {
      if (this.isEdit) {
        this.isRequestOnGoing = true
        let response = await this.fetchPayment(this.$route.params.id)
        this.formData = { ...this.formData, ...response.data.payment }
        this.formData.amount = response.data.payment.amount
        this.amount = this.formData.amount /100
        this.selectCustomer = response.data.payment.user && response.data.payment.user.id
        setInterval(()=> {
             this.customer = response.data.payment.user
        },200)
        this.formData.payment_date = moment(response.data.payment.payment_date).format('YYYY-MM-DD')
        this.paymentPrefix = response.data.payment_prefix
        this.paymentNumAttribute = response.data.nextPaymentNumber
        this.selectPaymentmethod = response.data.payment && response.data.payment.payment_method && response.data.payment.payment_method.id
        this.formData.payment_method = response.data.payment.payment_method
       
        if (response.data.payment.invoice !== null) {
          this.maxPayableAmount =
            parseInt(response.data.payment.amount) +
            parseInt(response.data.payment.invoice.due_amount)
           this.formData.invoice_id = response.data.payment.invoice && response.data.payment.invoice.id
           this.invoice = response.data.payment.invoice
        }
        let res = await this.fetchCustomFields({
          type: 'Payment',
          limit: 'all',
        })
        this.setEditCustomFields(
          response.data.payment.fields,
          res.data.customFields.data
        )

        if (this.formData.payment_method_id) {
          await this.fetchPaymentModes({ limit: 'all' })
        }

        if (this.formData.user_id) {
          await this.fetchCustomers({ limit: 'all' })
        }
        this.isRequestOnGoing = false
      } else {
        this.amount = this.formData.amount
        this.isRequestOnGoing = true
        this.checkAutoGenerate()
        this.setInitialCustomFields('Payment')
        this.formData.payment_date = this.$utils.currentDate()
        this.fetchPaymentModes({ limit: 'all' })
        await this.fetchCustomers({ limit: 'all' })
        if (this.$route.query.customer) {
          this.setPaymentCustomer(parseInt(this.$route.query.customer))
        }
        this.isRequestOnGoing = false
      }
      return true
    },
    setPaymentCustomer(id) {
      this.selectCustomer = this.customers.find((c) => {
        return c.id === id
      }).id
      this.customer = this.customers.find((c) => {
        return c.id === id
      })
    },
    async setInvoicePaymentData() {
      if(this.$route.params !== null && this.$route.params !== undefined && this.$route.params !== '') {
        let data = await this.fetchInvoice(this.$route.params.id)
        this.selectCustomer = data.data.invoice.user && data.data.invoice.user.id
        this.customer = data.data.invoice.user
        this.invoice = data.data.invoice
        this.amount = data.data.invoice.due_amount/100
        this.formData.amount = data.data.invoice.due_amount
        this.formData.invoice_id = data.data.invoice && data.data.invoice.id
      }
    },
    async setPaymentAmountByInvoiceData(id) {
      let data = await this.fetchInvoice(id)
      this.amount =data.data.invoice.due_amount
      this.formData.amount = data.data.invoice.due_amount
      this.maxPayableAmount = data.data.invoice.due_amount
    },
    async fetchCustomerInvoices(userId) {
      let data = {
        customer_id: userId,
        status: 'UNPAID',
      }
      let response = await this.fetchInvoices(data)
      this.invoiceList = response.data.invoices.data
    },
    async submitPaymentData() {
      let validate = await this.touchCustomField()
      
      this.$v.selectCustomer.$touch()
      this.$v.formData.$touch()
      if (this.$v.$invalid || validate.error) {
        return true
      }
        
      this.formData.payment_number =
        this.paymentPrefix + '-' + this.paymentNumAttribute

      if (this.isEdit) {
        let data = {
          editData: {
            ...this.formData,
            payment_method_id: this.formData.payment_method
              ? this.formData.payment_method.id
              : null,
            payment_date: moment(this.formData.payment_date).format(
              'YYYY-MM-DD'
            ),
          },
          id: this.$route.params.id,
        }

        try {
          this.isLoading = true
          let response = await this.updatePayment(data)

          if (response.data.success) {
            this.isLoading = false
            this.$router.push(
              `/admin/payments/${response.data.payment.id}/view`
            )
            window.toastr['success'](this.$t('payments.updated_message'))
            return true
          }

          if (response.data.error === 'invalid_amount') {
            window.toastr['error'](this.$t('invalid_amount_message'))
            return false
          }

          window.toastr['error'](response.data.error)
        } catch (err) {
          this.isLoading = false

          if (err.response.data.errors.payment_number) {
            window.toastr['error'](err.response.data.errors.payment_number)
            return true
          }

          window.toastr['error'](err.response.data.message)
        }
      } else {
        let data = {
          ...this.formData,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
        }

        this.isLoading = true

        try {
          let response = await this.addPayment(data)

          if (response.data.success) {
            this.$router.push(
              `/admin/payments/${response.data.payment.id}/view`
            )
            window.toastr['success'](this.$t('payments.created_message'))
            this.isLoading = true
            return true
          }

          if (response.data.error === 'invalid_amount') {
            window.toastr['error'](this.$t('invalid_amount_message'))
            return false
          }

          window.toastr['error'](response.data.error)
        } catch (err) {
          this.isLoading = false

          if (err.response.data.errors.payment_number) {
            window.toastr['error'](err.response.data.errors.payment_number)
            return true
          }

          window.toastr['error'](err.response.data.message)
        }
      }
    },
    onSelectNote(data) {
      this.formData.notes = '' + data.notes
      this.$refs.notePopup.close()
    },
  },
}
</script>
