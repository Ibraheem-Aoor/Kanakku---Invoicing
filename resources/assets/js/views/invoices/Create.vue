<template>
  <base-page class="relative invoice-create-page">
    <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="page-title">{{$tc('invoices.invoice', 2)}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$tc('general.home')}}</router-link></li>
									<li class="breadcrumb-item"><router-link to="/admin/invoices">{{$tc('invoices.invoice', 2)}}</router-link></li>
									<li class="breadcrumb-item active" v-if="$route.name === 'invoice.edit'">{{$tc('invoices.edit_invoice', 2)}}</li>
                  <li class="breadcrumb-item active">{{$tc('invoices.new_invoice', 2)}}</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-body">
									<form action="#" v-if="!isLoadingInvoice && !isLoadingData" @submit.prevent="submitForm">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>{{$t('payments.customer')}} <span class="text-danger">*</span></label>
													<select class="select form-control" v-model="selectedCustomer" :class="{ 'is-invalid': $v.selectedCustomer.$error }"  @change="$v.selectedCustomer.$touch()">
														<option>Select Customer</option>
														<option v-for="item in customers" :key="item.id" :value="item.id">{{item.name}}</option>
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>{{$t('invoices.invoice_date')}} <span class="text-danger">*</span></label>
													<div class="datemx">
													 <datepicker v-model.trim="newInvoice.invoice_date"
                                :editable="false"
                                :clearable="false"
                                :disabled-date="$utils.disabledAfterToday" :class="{ 'is-invalid': $v.newInvoice.invoice_date.$error }" @input="$v.newInvoice.invoice_date.$touch()"
                                value-type="format" format="YYYY-MM-DD"></datepicker>
                            <div v-if="$v.newInvoice.invoice_date.$error">{{invoiceDateError}}</div>
                          </div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>{{$t('invoices.due_date')}} <span class="text-danger">*</span></label>
													<div class="datemx">
														 <datepicker v-model.trim="newInvoice.due_date"
                                 :editable="false"
                                 :clearable="false"
                                :disabled-date="$utils.disabledAfterToday" :class="{ 'is-invalid': $v.newInvoice.due_date.$error }" @input="$v.newInvoice.due_date.$touch()"
                                value-type="format" format="YYYY-MM-DD"></datepicker>
                            <div v-if="$v.newInvoice.due_date.$error">{{dueDateError}}</div>
                          </div>
												</div>
											</div>
											<div class="col-md-4 mt-3">
												<div class="form-group">
													<label>{{$t('invoices.invoice_number')}} <span class="text-danger">*</span></label>
													<input type="text"  v-model="invoiceNumAttribute" :class="{ 'is-invalid': $v.invoiceNumAttribute.$error }" @input="$v.invoiceNumAttribute.$touch()" class="form-control">
                          <div v-if="$v.invoiceNumAttribute.$error">{{invoiceNumError}}</div>
                        </div>
											</div>
											<div class="col-md-4 mt-3">
												<div class="form-group">
													<label>{{$t('invoices.ref_number')}}</label>
													<input type="text" v-model="newInvoice.reference_number" :class="{ 'is-invalid': $v.newInvoice.reference_number.$error }" @input="$v.newInvoice.reference_number.$touch()" class="form-control">
                          <div v-if="$v.newInvoice.reference_number.$error">{{referenceError}}</div>
                        </div>
											</div>
										</div>
										<div class="table-responsive mt-4">
											<table class="table table-stripped table-center table-hover">
												<thead>
													<tr>
														<th>{{$tc('items.item', 2) }}</th>
														<th>{{$tc('invoices.item.quantity') }}</th>
														<th>{{$tc('invoices.item.price') }}</th>
                            <th v-if="discountPerItem === 'YES'">
                                {{ $t('invoices.item.discount') }}
                            </th>
														<th>{{$tc('invoices.item.amount') }}</th>
														<th>{{$tc('invoices.action') }}</th>
													</tr>
												</thead>
                         <tbody>
                            <invoice-item
                                  v-for="(item, index) in newInvoice.items"
                                  :key="item.id"
                                  :index="index"
                                  :item-data="item"
                                  :invoice-items="newInvoice.items"
                                  :currency="currency"
                                  :tax-per-item="taxPerItem"
                                  :discount-per-item="discountPerItem"
                                  @remove="removeItem"
                                  @update="updateItem"
                                  @itemValidate="checkItemsData"
                                   @addItem="addItem"
                            />
                          </tbody>
											</table>
										</div>
										<div class="table-responsive mt-4">
											<table class="table table-stripped table-center table-hover">
												<thead></thead>
												<tbody>
														<tr>
														<td></td>
														<td></td>
														<td></td>
														<td class="text-right">{{ $t('invoices.sub_total') }}</td>
														<td class="text-right"><div v-html="$utils.formatMoney(subtotal, currency)" /></td>
													</tr>
												<tr v-if="discountPerItem === 'NO' || discountPerItem === null">
														<td></td>
														<td></td>
														<td></td>
                            
														<td style="width: 20%" class="text-right"> {{ $t('invoices.item.discount') }}</td>
														<td class="text-right">
                              <div class="row float-right">
                                <div class="col-md-6"> <input type="text"  v-model="discount" :class="{ 'is-invalid': $v.newInvoice.discount_val.$error }" @input="$v.newInvoice.discount_val.$touch()" class="form-control"/></div>
                                <div class="col-md-6"> 
                                  <select class="form-control" v-model="discounttype">
                                    <option value="fixed">{{currency && currency.symbol || defaultCurrency }}</option>
                                    <option value="percentage">%</option>
                                  </select>
                                </div>
                             
                              </div>
                            </td>
													</tr>
                          	<tr v-if="taxPerItem === 'NO' || taxPerItem === null">
														<td></td>
														<td></td>
														<td></td>
                            
														<td class="text-right"> {{ $t('invoices.item.tax') }}</td>
														<td class="text-right" style="width: 20%" >
                               <div v-if="taxPerItem === 'NO' || taxPerItem === null">
                                      <tax
                                        v-for="(tax, index) in newInvoice.taxes"
                                        :index="index"
                                        :total="subtotalWithDiscount"
                                        :key="tax.id"
                                        :tax="tax"
                                        :taxes="newInvoice.taxes"
                                        :currency="currency"
                                        :total-tax="totalSimpleTax"
                                        @remove="removeInvoiceTax"
                                        @update="updateTax"
                                      />
                                        <div slot="activator" v-if="taxPerItem === 'NO' || taxPerItem === null" class="float-right pt-2 pb-4">
                                              <a  @click="openTaxModal">+ {{ $t('estimates.add_tax') }}</a>
                                            </div>   
                                       
                              </div>
                             
                            </td>
													</tr>
                          	<tr v-else-if="taxPerItem === 'YES' || taxPerItem !== null">
														<td></td>
														<td></td>
														<td></td>
                            <td></td>
														<td class="text-right">
                                  <div v-if="taxPerItem === 'YES' || taxPerItem !== null">
                                        <div
                                            v-for="tax in allTaxes"
                                            :key="tax.tax_type_id"
                                            class="flex items-center justify-between w-full"
                                        >
                                          <label
                                            class="m-0 text-sm font-semibold leading-5 text-gray-500 uppercase"
                                            >{{ tax.name }} - {{ tax.percent }}%
                                          </label>
                                          <label
                                            class="flex items-center justify-center m-0 text-lg text-black uppercase"
                                            style="font-size: 18px"
                                          >
                                            <div v-html="$utils.formatMoney(tax.amount, currency)" />
                                          </label>
                                        </div>
                                  </div>
                            </td>
													</tr>
													<tr>
														<td></td>
														<td></td>
														<td></td>
														<td class="text-right">{{ $t('invoices.total') }}</td>
														<td class="text-right"><div v-html="$utils.formatMoney(total, currency)" /></td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="text-right mt-4">
											<button type="submit" :disabled="isLoading" class="btn btn-primary"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>{{ $t('invoices.save_invoice') }}</button>
										</div>
									</form>
                  <base-loader v-else />
								</div>
							</div>
						</div>
					</div>
           <tax-select-popup ref="taxmodal"
              :taxes="newInvoice.taxes"
              @select="onSelectTax"
            />
				</div>
    <!-- <base-loader v-else /> -->
  </base-page>
</template>

<script>
import draggable from 'vuedraggable'
import InvoiceItem from './Item'
import InvoiceStub from '../../stub/invoice'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import Guid from 'guid'
import TaxStub from '../../stub/tax'
import Tax from './InvoiceTax'
import { PlusSmIcon } from '@vue-hero-icons/outline'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'
import invoice from '../../stub/invoice'

const {
  required,
  between,
  maxLength,
  numeric,
} = require('vuelidate/lib/validators')

export default {
  components: {
    InvoiceItem,
    Tax,
    draggable,
    PlusSmIcon,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
  },
  mixins: [CustomFieldsMixin],

  data() {
    return {
      newInvoice: {
        invoice_date: null,
        due_date: null,
        invoice_number: null,
        user_id: null,
        invoice_template_id: 1,
        sub_total: null,
        total: null,
        tax: null,
        notes: null,
        discount_type: 'fixed',
        discount_val: 0,
        discount: 0,
        reference_number: null,
        items: [
          {
            ...InvoiceStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        taxes: [],
      },
      discounttype: 'fixed',
      selectedCurrency: '',
      taxPerItem: null,
      discountPerItem: null,
      isLoadingInvoice: false,
      isLoadingData: false,
      isLoading: false,
      maxDiscount: 0,
      invoicePrefix: null,
      invoiceNumAttribute: null,
      InvoiceFields: [
        'customer',
        'customerCustom',
        'company',
        'invoice',
        'invoiceCustom',
      ],
      selectedCustomer: null,
    }
  },

  validations() {
    return {
      newInvoice: {
        invoice_date: {
          required,
        },
        due_date: {
          required,
        },
        discount_val: {
          between: between(0, this.subtotal),
        },
        reference_number: {
          maxLength: maxLength(255),
        },
      },
      selectedCustomer: {
        required,
      },
      invoiceNumAttribute: {
        required,
        numeric,
      },
    }
  },

  computed: {
    ...mapGetters('company', ['itemDiscount']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('notes', ['notes']),

    ...mapGetters('customer', ['customers']),

    ...mapGetters('invoice', [
      'getTemplateId',
      'selectedNote',
    ]),

    ...mapGetters('invoiceTemplate', ['getInvoiceTemplates']),

    currency() {
      return this.selectedCurrency
    },

    pageTitle() {
      if (this.isEdit) {
        return this.$t('invoices.edit_invoice')
      }
      return this.$t('invoices.new_invoice')
    },

    isEdit() {
      if (this.$route.name === 'invoices.edit') {
        return true
      }
      return false
    },

    subtotalWithDiscount() {
      return this.subtotal - this.newInvoice.discount_val
    },

    total() {
      return this.subtotalWithDiscount + this.totalTax
    },

    subtotal() {
      return this.newInvoice.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },

    discount: {
      get: function () {
        return this.newInvoice.discount
      },
      set: function (newValue) {
        if (this.newInvoice.discount_type === 'percentage') {
          this.newInvoice.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.newInvoice.discount_val = Math.round(newValue * 100)
        }

        this.newInvoice.discount = newValue
      },
    },

    totalSimpleTax() {
      return Math.round(
        window._.sumBy(this.newInvoice.taxes, function (tax) {
          if (!tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },

    totalCompoundTax() {
      return Math.round(
        window._.sumBy(this.newInvoice.taxes, function (tax) {
          if (tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },

    totalTax() {
      if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
        return this.totalSimpleTax + this.totalCompoundTax
      }

      return Math.round(
        window._.sumBy(this.newInvoice.items, function (tax) {
          return tax.tax
        })
      )
    },

    allTaxes() {
      let taxes = []

      this.newInvoice.items.forEach((item) => {
        item.taxes.forEach((tax) => {
          let found = taxes.find((_tax) => {
            return _tax.tax_type_id === tax.tax_type_id
          })

          if (found) {
            found.amount += tax.amount
          } else if (tax.tax_type_id) {
            taxes.push({
              tax_type_id: tax.tax_type_id,
              amount: tax.amount,
              percent: tax.percent,
              name: tax.name,
            })
          }
        })
      })

      return taxes
    },

    invoiceDateError() {
      if (!this.$v.newInvoice.invoice_date.$error) {
        return ''
      }
      if (!this.$v.newInvoice.invoice_date.required) {
        return this.$t('validation.required')
      }
    },

    dueDateError() {
      if (!this.$v.newInvoice.due_date.$error) {
        return ''
      }
      if (!this.$v.newInvoice.due_date.required) {
        return this.$t('validation.required')
      }
    },

    invoiceNumError() {
      if (!this.$v.invoiceNumAttribute.$error) {
        return ''
      }

      if (!this.$v.invoiceNumAttribute.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.invoiceNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    referenceError() {
      if (!this.$v.newInvoice.reference_number.$error) {
        return ''
      }

      if (!this.$v.newInvoice.reference_number.maxLength) {
        return this.$tc('validation.ref_number_maxlength')
      }
    },
  },

  watch: {
    selectedCustomer(newVal) {
      newVal = this.customers.find(item => item.id === newVal)
      if (newVal && newVal.currency) {
        this.selectedCurrency = newVal.currency
      } else {
        this.selectedCurrency = this.defaultCurrency
      }
    },
    discounttype(newValue) {
        if(newValue === 'fixed') {
            this.selectFixed()
        } else {
            this.selectPercentage()
        }
    },  
    selectedNote() {
      if (this.selectedNote) {
        this.newInvoice.notes = this.selectedNote
      }
    },

    subtotal(newValue) {
      if (this.newInvoice.discount_type === 'percentage') {
        this.newInvoice.discount_val =
          (this.newInvoice.discount * newValue) / 100
      }
    },
  },

  async created() {
    await this.loadData()
    await this.fetchInitialData()
    window.hub.$on('newTax', this.onSelectTax)
    if (this.$route.query.customer) {
      this.selectedCustomer = parseInt(this.$route.query.customer)
    }
  },
  
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('invoice', [
      'addInvoice',
      'fetchInvoice',
      'getInvoiceNumber',
      'selectCustomer',
      'updateInvoice',
      'resetSelectedNote',
    ]),

    ...mapActions('invoiceTemplate', ['fetchInvoiceTemplates']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('customFields', ['fetchCustomFields']),

    ...mapActions('customer', ['fetchCustomers']),


    selectFixed() {
      if (this.newInvoice.discount_type === 'fixed') {
        return
      }

      this.newInvoice.discount_val = Math.round(this.newInvoice.discount * 100)
      this.newInvoice.discount_type = 'fixed'
    },
    openTaxModal() {
        this.$refs.taxmodal.show()
    },
    selectPercentage() {
      if (this.newInvoice.discount_type === 'percentage') {
        return
      }

      this.newInvoice.discount_val =
        (this.subtotal * this.newInvoice.discount) / 100

      this.newInvoice.discount_type = 'percentage'
    },

    updateTax(data) {
      Object.assign(this.newInvoice.taxes[data.index], { ...data.item })
    },

    async fetchInitialData() {
      this.isLoadingData = true
       await this.fetchCustomers({limit: 'all'})
      if (!this.isEdit) {
        let response = await this.fetchCompanySettings([
          'discount_per_item',
          'tax_per_item',
        ])

        if (response.data) {
          this.discountPerItem = response.data.discount_per_item
          this.taxPerItem = response.data.tax_per_item
        }
      }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
        }),
        this.fetchInvoiceTemplates(),
        this.resetSelectedNote(),
        this.getInvoiceNumber(),
        this.fetchCompanySettings(['invoice_auto_generate']),
      ])
        .then(async ([res1, res2, res3, res4, res5]) => {
          if (
            !this.isEdit &&
            res5.data &&
            res5.data.invoice_auto_generate === 'YES'
          ) {
            if (res4.data) {
              this.invoiceNumAttribute = res4.data.nextNumber
              this.invoicePrefix = res4.data.prefix
            }
          } else {
            this.invoicePrefix = res4.data.prefix
          }

          // this.discountPerItem = res5.data.discount_per_item
          // this.taxPerItem = res5.data.tax_per_item
          this.isLoadingData = false
        })
        .catch((error) => {
          console.log(error)
        })
    },

    async loadData() {
      if (this.isEdit) {
        this.isLoadingInvoice = true

        Promise.all([
          this.fetchInvoice(this.$route.params.id),
          this.fetchCustomFields({
            type: 'Invoice',
            limit: 'all',
          }),
          this.fetchTaxTypes({ limit: 'all' }),
        ])
          .then(async ([res1, res2]) => {
            if (res1.data) {
              this.selectedCustomer = res1.data.invoice && res1.data.invoice.user_id
              this.newInvoice = res1.data.invoice
              this.formData = { ...this.formData, ...res1.data.invoice }

              this.newInvoice.invoice_date = moment(
                res1.data.invoice.invoice_date).format('YYYY-MM-DD')

              this.newInvoice.due_date = moment(
                res1.data.invoice.due_date).format('YYYY-MM-DD')

              this.discountPerItem = res1.data.invoice.discount_per_item
              this.selectedCurrency = this.defaultCurrency
              this.invoiceNumAttribute = res1.data.nextInvoiceNumber
              this.invoicePrefix = res1.data.invoicePrefix
              this.taxPerItem = res1.data.invoice.tax_per_item
              this.discounttype = res1.data.invoice.discount_type
              let fields = res1.data.invoice.fields

              if (res2.data) {
                let customFields = res2.data.customFields.data
                this.setEditCustomFields(fields, customFields)
              }
            }

            this.isLoadingInvoice = false
          })
          .catch((error) => {
            console.log(error)
          })

        return true
      }

      this.isLoadingInvoice = true
      await this.setInitialCustomFields('Invoice')
      await this.fetchTaxTypes({ limit: 'all' })
      this.selectedCurrency = this.defaultCurrency
      this.newInvoice.invoice_date = moment().format('YYYY-MM-DD')
      this.newInvoice.due_date = moment().add(7, 'days').format('YYYY-MM-DD')

      this.isLoadingInvoice = false
    },

    openTemplateModal() {
      this.openModal({
        title: this.$t('general.choose_template'),
        componentName: 'InvoiceTemplate',
        data: this.getInvoiceTemplates,
      })
    },

    addItem() {
      this.newInvoice.items.push({
        ...InvoiceStub,
        id: Guid.raw(),
        taxes: [{ ...TaxStub, id: Guid.raw() }],
      })
    },

    removeItem(index) {
      this.newInvoice.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.newInvoice.items[data.index], { ...data.item })
    },

    async submitForm() {
      // return
      let validate = await this.touchCustomField()

      if (!this.checkValid() || validate.error) {
        return false
      }

      this.isLoading = true
      this.newInvoice.invoice_number =
        this.invoicePrefix + '-' + this.invoiceNumAttribute

      let data = {
        ...this.formData,
        ...this.newInvoice,
        sub_total: this.subtotal,
        total: this.total,
        tax: this.totalTax,
        user_id: null,
        invoice_template_id: this.getTemplateId,
      }

      if (this.selectedCustomer != null) {
        data.user_id = this.selectedCustomer
      }

      if (this.$route.name === 'invoices.edit') {
        this.submitUpdate(data)
        return
      }

      this.submitCreate(data)
    },

    submitCreate(data) {
      this.addInvoice(data)
        .then((res) => {
          if (res.data) {
            this.$router.push(`/admin/invoices/${res.data.invoice.id}/view`)

            window.toastr['success'](this.$t('invoices.created_message'))
          }

          this.isLoading = false
        })
        .catch((err) => {
          this.isLoading = false
        })
    },

    submitUpdate(data) {
      this.updateInvoice(data)
        .then((res) => {
          this.isLoading = false
          if (res.data.success) {
            this.$router.push(`/admin/invoices/${res.data.invoice.id}/view`)
            window.toastr['success'](this.$t('invoices.updated_message'))
          }

          if (res.data.error === 'invalid_due_amount') {
            window.toastr['error'](
              this.$t('invoices.invalid_due_amount_message')
            )
          }
        })
        .catch((err) => {
          this.isLoading = false
        })
    },

    checkItemsData(index, isValid) {
      this.newInvoice.items[index].valid = isValid
    },

    onSelectTax(selectedTax) {
      let amount = 0

      if (selectedTax.compound_tax && this.subtotalWithDiscount) {
        amount = Math.round(
          ((this.subtotalWithDiscount + this.totalSimpleTax) *
            selectedTax.percent) /
            100
        )
      } else if (this.subtotalWithDiscount && selectedTax.percent) {
        amount = Math.round(
          (this.subtotalWithDiscount * selectedTax.percent) / 100
        )
      }

      this.newInvoice.taxes.push({
        ...TaxStub,
        id: Guid.raw(),
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_type_id: selectedTax.id,
        amount,
      })

      // if (this.$refs) {
      //   this.$refs.taxModal.close()
      //   this.$refs.taxmodal.show()
      // }
    },

    removeInvoiceTax(index) {
      this.newInvoice.taxes.splice(index, 1)
    },

    checkValid() {
      this.$v.newInvoice.$touch()
      this.$v.selectedCustomer.$touch()
      this.$v.invoiceNumAttribute.$touch()

      window.hub.$emit('checkItems')
      let isValid = true
      this.newInvoice.items.forEach((item) => {
        if (!item.valid) {
          isValid = false
        }
      })
      if (
        !this.$v.selectedCustomer.$invalid &&
        !this.$v.invoiceNumAttribute.$invalid &&
        this.$v.newInvoice.$invalid === false &&
        isValid === true
      ) {
        return true
      }
      return false
    },
    onSelectNote(data) {
      this.newInvoice.notes = '' + data.notes
      this.$refs.notePopup.close()
    },
  },
}
</script>

<style lang="scss">
.invoice-create-page {
  .invoice-foot {
    .invoice-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .invoice-foot {
      .invoice-total {
        min-width: 384px;
      }
    }
  }
}
</style>
