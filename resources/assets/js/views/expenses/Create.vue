<template>
  <base-page class="relative">
    <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="page-title">{{$tc('expenses.expense', 2)}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link>
									</li>
									<li class="breadcrumb-item"><router-link to="/admin/expenses">{{$tc('expenses.expense', 2)}}</router-link>
									</li>
									<li v-if="$route.name === 'expenses.edit'" class="breadcrumb-item active">{{$t('expenses.edit_expense')}}</li>
									<li v-else class="breadcrumb-item active">{{$t('expenses.new_expense')}}</li>
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
									<form action="#" @submit.prevent="sendData">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>{{$t('expenses.category')}} <span class="text-danger">*</span></label>
                          
													<select class="select form-control" v-model="formData.expense_category_id" :class="{ 'is-invalid': $v.formData.expense_category_id.$error }" @input="$v.formData.expense_category_id.$touch()">
														<option>{{$t('expenses.categories.select_a_category')}}</option>
														<option v-for="item in categories" :key="item.id" :value="item.id">{{item.name}}</option>
													</select>
                          <div v-if="$v.formData.expense_category_id.$error" class="invalid-feedback">{{categoryError}}</div>
												</div>
                        	<div class="form-group">
													<label>{{$t('expenses.amount')}} <span class="text-danger">*</span></label>
													<input  type="text" v-model="amount" v-money="money" class="form-control" :class="{ 'is-invalid': $v.formData.amount.$error }"   @input="$v.formData.amount.$touch()">
                          <div v-if="$v.formData.amount.$error" class="invalid-feedback">{{amountError}}</div>
                        </div>
												<div class="form-group" >
													<label>{{$t('expenses.note')}}</label>
													<textarea rows="5" cols="5" class="form-control"  v-model="formData.notes" :class="{ 'is-invalid': $v.formData.notes.$error }"  @input="$v.formData.notes.$touch()"></textarea>
                          <div v-if="$v.formData.notes.$error" class="invalid-feedback">{{notesError}}</div>
                        </div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>{{$t('expenses.expense_date')}} <span class="text-danger">*</span></label>
													<div class="datemx">
														 <datepicker v-model="formData.expense_date"
                                  :editable="false"
                                  :clearable="false"
                                  :disabled-date="$utils.disabledAfterToday" :class="{ 'is-invalid': $v.formData.expense_date.$error }" @input="$v.formData.expense_date.$touch()"
                                  value-type="format" format="YYYY-MM-DD">
                              </datepicker>
                              <div v-if="$v.formData.expense_date.$error" class="invalid-feedback"></div>
													</div>
												</div>
												<div class="form-group">
													<label>{{$t('expenses.customer')}}</label>
													<select class="select form-control" v-model="selectCustomer">
														<option>{{$t('customers.select_a_customer')}}</option>
														<option v-for="item in customers" :key="item.id" :value="item.id">{{item.name}}</option>
													</select>
												</div>
                        	<div class="form-group">
													<label>{{$t('expenses.receipt')}}</label>
                            <div v-if="previewReceipt">
                              <div class="dropzone-wrapper text-center">
                                <img :src="previewReceipt" @click="changeImage" id="imgsource" name="img_logo" @change="onFileChange" class="img-avatar col-md-4" style="border:none !important;">
                              </div>
                            </div>
                            <div v-else class="dropzone-wrapper">
                              <div class="dropzone-desc">
                                <i class="glyphicon glyphicon-download-alt"></i>
                                <p>Choose an image file or drag it here.</p>
                              </div>
                              <input type="file" name="img_logo" @change="onFileChange" class="dropzone">
                            </div>
												</div>
                        <input type="file" name="img_logo" @change="onFileChange" id="filechange"  class="d-none">
												<div class="text-right mt-4">
													<button type="submit" :disabled="isLoading" class="btn btn-primary" ><i  v-if="isLoading" class="fas fa-spinner fa-spin"></i> {{
                            isEdit
                              ? $t('expenses.update_expense')
                              : $t('expenses.save_expense')
                          }}</button>
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
import moment from 'moment'
import { mapActions, mapGetters } from 'vuex'
const { required, minValue, maxLength, alphaNum } = require('vuelidate/lib/validators')
import { DownloadIcon } from '@vue-hero-icons/outline'
import { CloudUploadIcon, ShoppingCartIcon } from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'

export default {
  components: {
    CloudUploadIcon,
    ShoppingCartIcon,
    DownloadIcon,
  },
  mixins: [CustomFieldsMixin],

  props: {
    addname: {
      type: String,
      default: '',
    },
  },

  data() {
    return {
      amount: 100,
      formData: {
        expense_category_id: null,
        expense_date: this.$utils.currentDate(),
        amount: 100,
        notes: '',
        user_id: null,
      },
      showPreview: false,
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
      isRequestOnGoing: false,
      isReceiptAvailable: false,
      isLoading: false,
      category: null,
      previewReceipt: null,
      fileSendUrl: '/api/v1/expenses',
      customer: null,
      fileObject: null,
      selectCustomer: null,
    }
  },

  validations: {
    formData: {
      expense_date: {
        required,
      },
      expense_category_id: {
        required,
      },
      amount: {
        required,
        minValue: minValue(0.1),
        maxLength: maxLength(20),
      },
      notes: {
        maxLength: maxLength(65000),
      },
    },
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),

    pageTitle() {
      if (this.$route.name === 'expenses.edit') {
        return this.$t('expenses.edit_expense')
      }
      return this.$t('expenses.new_expense')
    },

    isEdit() {
      if (this.$route.name === 'expenses.edit') {
        return true
      }
      return false
    },

    ...mapGetters('category', ['categories']),

    ...mapGetters('customer', ['customers']),

    ...mapGetters('company', ['getSelectedCompany']),

    getReceiptUrl() {
      if (this.isEdit) {
        return `/expenses/${this.$route.params.id}/receipt`
      }
    },

    categoryError() {
      if (!this.$v.formData.expense_category_id.$error) {
        return ''
      }
      if (!this.$v.formData.expense_category_id.required) {
        return this.$t('validation.required')
      }
    },

    dateError() {
      if (!this.$v.formData.expense_date.$error) {
        return ''
      }
      if (!this.$v.formData.expense_date.required) {
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
      if (!this.$v.formData.amount.maxLength) {
        return this.$t('validation.price_maxlength')
      }
      if (!this.$v.formData.amount.minValue) {
        return this.$t('validation.price_minvalue')
      }
    },

    notesError() {
      if (!this.$v.formData.notes.$error) {
        return ''
      }
       if (!this.$v.formData.notes.alphaNum) {
        return 'Notes should contain Alphabets and numbers'
      }
      if (!this.$v.formData.notes.maxLength) {
        return this.$t('validation.notes_maxlength')
      }
    },
  },

  watch: {
    selectCustomer(newValue) {
      this.customer = this.customers.find(item => item.id === newValue)
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
  },
  created() {
    this.money.prefix = this.defaultCurrencyForInput && this.defaultCurrencyForInput.prefix
  },
  mounted() {
    this.$v.formData.$reset()
    this.loadData()

    window.hub.$on('newCategory', (val) => {
      this.category = val
    })
  },

  methods: {
    ...mapActions('expense', [
      'getExpenseReceipt',
      'addExpense',
      'updateExpense',
      'fetchExpense',
    ]),

    ...mapActions('modal', ['openModal']),

    ...mapActions('category', ['fetchCategories']),

    ...mapActions('customer', ['fetchCustomers']),

    openCategoryModal() {
      this.openModal({
        title: this.$t('settings.expense_category.add_category'),
        componentName: 'CategoryModal',
      })
    },
    onFileChange(e) {
      let input = e.target;
      if (input.files && input.files[0]) {
      let reader = new FileReader();
      this.fileObject = input.files[0]
      reader.onload = (e) => {
          this.previewReceipt = e.target.result;
      };

     reader.readAsDataURL(input.files[0]);
  }
    },
    async getReceipt() {
      let res = await this.getExpenseReceipt(this.$route.params.id)

      if (res.data.error) {
        this.isReceiptAvailable = false
        return true
      }

      this.isReceiptAvailable = true
      this.previewReceipt = res.data.image
    },
    changeImage() {
      document.getElementById("filechange").click()
    },
    setExpenseCustomer(id) {
      this.selectCustomer = this.customers && this.customers.find((c) => {
        return c.id == id
      }).id
      this.customer = this.customers && this.customers.find((c) => {
        return c.id == id
      })
    },
    async loadData() {
      this.isRequestOnGoing = true
      await this.fetchCategories({ limit: 'all' })
      await this.fetchCustomers({ limit: 'all' })
      if (this.isEdit) {
        this.isRequestOnGoing = true
        let response = await this.fetchExpense(this.$route.params.id)

        this.formData = { ...this.formData, ...response.data.expense }

        this.formData.expense_date = moment(
          this.formData.expense_date
        ).format("YYYY-MM-DD")
        this.formData.amount = response.data.expense.amount
        this.amount = this.formData.amount /100
        this.fileSendUrl = `/api/v1/expenses/${this.$route.params.id}`

        if (response.data.expense.expense_category_id) {
          this.expense_category_id = this.categories && this.categories.find(
            (category) =>
              category.id === response.data.expense.expense_category_id
          ).id
          this.category = this.categories.find(
            (category) =>
              category.id === response.data.expense.expense_category_id
          )
        }

        if (response.data.expense.user_id) {
          this.selectCustomer = this.customers && this.customers.find(
            (customer) => customer.id === response.data.expense.user_id
          ).id
          this.customer = this.customers.find(
            (customer) => customer.id === response.data.expense.user_id
          )
        }

        let res = await this.fetchCustomFields({
          type: 'Expense',
          limit: 'all',
        })

        this.setEditCustomFields(
          response.data.expense.fields,
          res.data.customFields.data
        )

        this.getReceipt()
        this.isRequestOnGoing = false
        return true
      } else {
        
         this.amount = this.formData.amount
          await this.setInitialCustomFields('Expense')
          if (this.$route.query.customer) {
            this.setExpenseCustomer(parseInt(this.$route.query.customer))
          }
          this.isRequestOnGoing = false
      }
     
    },

    async sendData() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      let data = new FormData()

      if (this.fileObject) {
        data.append('attachment_receipt', this.fileObject)
      }
      data.append('expense_category_id', this.formData.expense_category_id)
      data.append(
        'expense_date',
        moment(this.formData.expense_date).format('YYYY-MM-DD')
      )
      data.append('amount', this.formData.amount)
      data.append('notes', this.formData.notes ? this.formData.notes : '')
      data.append('user_id', this.customer ? this.customer.id : '')
      data.append('customFields', JSON.stringify(this.formData.customFields))

      if (this.isEdit) {
        this.isLoading = true
        data.append('_method', 'PUT')
        let response = await this.updateExpense({
          id: this.$route.params.id,
          editData: data,
        })

        if (response.data.success) {
          this.isLoading = false
          window.toastr['success'](this.$t('expenses.updated_message'))
          this.$router.push('/admin/expenses')
          return true
        }
        window.toastr['error'](response.data.error)
      } else {
        this.isLoading = true
        let response = await this.addExpense(data)
        this.isLoading = false

        if (response.data.success) {
          window.toastr['success'](this.$t('expenses.created_message'))
          this.$router.push('/admin/expenses')
          return true
        }
        window.toastr['success'](response.data.success)
      }
    },
  },
}
</script>
