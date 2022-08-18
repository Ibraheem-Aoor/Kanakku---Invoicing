<template>
  <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="page-title">{{$t('estimates.title')}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link></li>
									<li class="breadcrumb-item"><router-link to="/admin/estimates">{{$tc('estimates.estimate', 2)}}</router-link></li>
									<li class="breadcrumb-item active" v-if="$route.name === 'estimates.edit'">{{$t('estimates.edit_estimate')}}</li>
                  <li class="breadcrumb-item active" v-else>{{$t('estimates.new_estimate')}}</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<div class="row">
            <base-loader v-if="isLoadingEstimate" :show-bg-overlay="true" />
						<div class="col-md-12">
							<div class="card">
								<div class="card-body">
									<form v-if="!isLoadingEstimate && !isLoadingData" @submit.prevent="submitForm">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>{{$t('payments.customer')}}<span class="text-danger">*</span></label>
													<select class="select form-control" v-model="selectedCustomer" :class="{ 'is-invalid': $v.selectedCustomer.$error }"  @change="$v.selectedCustomer.$touch()">
														<option>Select Customer</option>
														<option v-for="item in customers" :key="item.id" :value="item.id">{{item.name}}</option>
													</select>
                          <div class="invalid-feedback" v-if="$v.selectedCustomer.$error">{{estimateDateError}}</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>{{$t('reports.estimates.estimate_date')}} <span class="text-danger">*</span></label>
                          	<div class="datemx">
                                <datepicker v-model.trim="newEstimate.estimate_date"
                                 :editable="false"
                                  :clearable="false"
                                :disabled-date="$utils.disabledAfterToday" :class="{ 'is-invalid': $v.newEstimate.estimate_date.$error }" @input="$v.newEstimate.estimate_date.$touch()"
                                value-type="format" format="YYYY-MM-DD"></datepicker>
                                <div class="invalid-feedback" v-if="$v.newEstimate.estimate_date.$error">{{estimateDateError}}</div>
                            </div>
                        </div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>{{$t('reports.estimates.due_date')}} <span class="text-danger">*</span></label>
													<div class="datemx">
														<datepicker v-model.trim="newEstimate.expiry_date"
                             :editable="false"
                             :clearable="false"
                              :class="{ 'is-invalid': $v.newEstimate.expiry_date.$error }" @input="$v.newEstimate.expiry_date.$touch()"
												      value-type="format" format="YYYY-MM-DD"></datepicker>
                            <div class="invalid-feedback" v-if="$v.newEstimate.expiry_date.$error">{{estimateDateError}}</div>
													</div>
												</div>
											</div>
											<div class="col-md-4 mt-3">
												<div class="form-group">
													<label>{{$t('estimates.estimate_number')}} <span class="text-danger">*</span></label>
													<input type="text"  v-model="estimateNumAttribute" :class="{ 'is-invalid': $v.estimateNumAttribute.$error }" @input="$v.estimateNumAttribute.$touch()" class="form-control">
                          <div class="invalid-feedback" v-if="$v.estimateNumAttribute.$error">{{estimateNumError}}</div>
                        </div>
											</div>
											<div class="col-md-4 mt-3">
												<div class="form-group">
													<label>{{$t('estimates.ref_number')}}</label>
													<input type="text" v-model="newEstimate.reference_number" :class="{ 'is-invalid': $v.newEstimate.reference_number.$error }" @input="$v.newEstimate.reference_number.$touch()" class="form-control">
                          <div class="invalid-feedback" v-if="$v.newEstimate.reference_number.$error">{{referenceNumError}}</div>
                      	</div>
											</div>
										</div>
										<div class="table-responsive mt-4">
											<table class="table table-stripped table-center table-hover">
												<thead>
													<tr>
														<th>{{ $tc('items.item', 2) }}</th>
														<th>{{ $t('estimates.item.quantity') }}</th>
														<th>{{ $t('estimates.item.price') }}</th>
                            <th v-if="discountPerItem === 'YES'">{{ $t('estimates.discount') }}</th>
														<th>{{ $t('estimates.item.amount') }}</th>
														<th>{{ $t('estimates.action') }}</th>
													</tr>
												</thead>
                        <tbody>
                                <estimate-item
                                  v-for="(item, index) in newEstimate.items"
                                  :key="item.id"
                                  :index="index"
                                  :item-data="item"
                                  :currency="currency"
                                  :estimate-items="newEstimate.items"
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
														<td class="text-right">{{ $t('estimates.sub_total') }}</td>
														<td class="text-right"><div v-html="$utils.formatMoney(subtotal, currency)" /></td>
													</tr>
													<tr v-if="discountPerItem === 'NO' || discountPerItem === null">
														<td></td>
														<td></td>
														<td></td>
                            
														<td style="width: 20%" class="text-right">{{ $t('estimates.discount') }}</td>
														<td class="text-right">
                              <div class="row float-right">
                                <div class="col-md-6"> <input type="text"  v-model="discount" :class="{ 'is-invalid': $v.newEstimate.discount_val.$error }" @input="$v.newEstimate.discount_val.$touch()" class="form-control"/></div>
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
                            
														<td class="text-right">{{ $t('estimates.tax') }}</td>
														<td class="text-right" style="width: 20%" >
                               <div v-if="taxPerItem === 'NO' || taxPerItem === null">
                                      <tax
                                        v-for="(tax, index) in newEstimate.taxes"
                                        :index="index"
                                        :total="subtotalWithDiscount"
                                        :key="tax.id"
                                        :tax="tax"
                                        :taxes="newEstimate.taxes"
                                        :currency="currency"
                                        :total-tax="totalSimpleTax"
                                        @remove="removeEstimateTax"
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
														<td class="text-right">{{ $t('estimates.total') }}:</td>
														<td class="text-right"><div v-html="$utils.formatMoney(total, currency)" /></td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="text-right mt-4">
											<button type="submit" :disabled="isLoading" class="btn btn-primary"><i  v-if="isLoading" class="fas fa-spinner fa-spin"></i> {{ $t('estimates.save_estimate') }}</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
          <tax-select-popup ref="taxmodal"
                                            :taxes="newEstimate.taxes"
                                            @select="onSelectTax"
                                          />
				</div>
        
    <!-- <base-loader v-else /> -->
  </base-page>
</template>

<script>
import draggable from 'vuedraggable'
import EstimateItem from './Item'
import EstimateStub from '../../stub/estimate'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import Guid from 'guid'
import TaxStub from '../../stub/tax'
import Tax from './EstimateTax'
import { PlusSmIcon, PencilIcon } from '@vue-hero-icons/outline'
import CustomFieldsMixin from '../../mixins/customFields'

import {
  ChevronDownIcon,
  HashtagIcon,
  ShoppingCartIcon,
} from '@vue-hero-icons/solid'

const {
  required,
  between,
  maxLength,
  numeric,
} = require('vuelidate/lib/validators')

export default {
  components: {
    EstimateItem,
    Tax,
    draggable,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusSmIcon,
    HashtagIcon,
  },
  mixins: [CustomFieldsMixin],

  data() {
    return {
      newEstimate: {
        estimate_date: null,
        expiry_date: null,
        estimate_number: null,
        user_id: null,
        estimate_template_id: 1,
        sub_total: null,
        total: null,
        tax: null,
        notes: null,
        discount_type: 'fixed',
        discount_val: 0,
        reference_number: null,
        discount: 0,
        items: [
          {
            ...EstimateStub,
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
      isLoadingEstimate: false,
      isLoadingData: false,
      isLoading: false,
      maxDiscount: 0,
      estimateNumAttribute: null,
      estimatePrefix: null,
      isRequestOnGoing: false,
      EstimateFields: [
        'customer',
        'company',
        'customerCustom',
        'estimate',
        'estimateCustom',
      ],
      customerId: null,
      selectedCustomer: null,
    }
  },

  validations() {
    return {
      newEstimate: {
        estimate_date: {
          required,
        },
        expiry_date: {
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
      estimateNumAttribute: {
        required,
        numeric,
      },
    }
  },

  computed: {
    ...mapGetters('company', ['itemDiscount']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('customer', ['customers']),

    ...mapGetters('estimate', [
      'getTemplateId',
      'selectedNote',
    ]),

    ...mapGetters('estimateTemplate', ['getEstimateTemplates']),

    pageTitle() {
      if (this.isEdit) {
        return this.$t('estimates.edit_estimate')
      }
      return this.$t('estimates.new_estimate')
    },

    isEdit() {
      if (this.$route.name === 'estimates.edit') {
        return true
      }
      return false
    },

    subtotalWithDiscount() {
      return this.subtotal - this.newEstimate.discount_val
    },

    total() {
      return this.subtotalWithDiscount + this.totalTax
    },

    subtotal() {
      return this.newEstimate.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },

    discount: {
      get: function () {
        return this.newEstimate.discount
      },
      set: function (newValue) {
        if (this.newEstimate.discount_type === 'percentage') {
          this.newEstimate.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.newEstimate.discount_val = Math.round(newValue * 100)
        }

        this.newEstimate.discount = newValue
      },
    },

    totalSimpleTax() {
      return window._.sumBy(this.newEstimate.taxes, function (tax) {
        if (!tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },

    totalCompoundTax() {
      return window._.sumBy(this.newEstimate.taxes, function (tax) {
        if (tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },

    totalTax() {
      if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
        return this.totalSimpleTax + this.totalCompoundTax
      }

      return window._.sumBy(this.newEstimate.items, function (tax) {
        return tax.tax
      })
    },
    currency() {
      return this.selectedCurrency
    },
    allTaxes() {
      let taxes = []

      this.newEstimate.items.forEach((item) => {
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

    estimateDateError() {
      if (!this.$v.newEstimate.estimate_date.$error) {
        return ''
      }

      if (!this.$v.newEstimate.estimate_date.required) {
        return this.$t('validation.required')
      }
    },

    estimateNumError() {
      if (!this.$v.estimateNumAttribute.$error) {
        return ''
      }

      if (!this.$v.estimateNumAttribute.required) {
        return this.$tc('estimates.errors.required')
      }

      if (!this.$v.estimateNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    expiryDateError() {
      if (!this.$v.newEstimate.expiry_date.$error) {
        return ''
      }

      if (!this.$v.newEstimate.expiry_date.required) {
        return this.$t('validation.required')
      }
    },

    referenceNumError() {
      if (!this.$v.newEstimate.reference_number.$error) {
        return ''
      }

      if (!this.$v.newEstimate.reference_number.maxLength) {
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
    selectedNote() {
      if (this.selectedNote) {
        this.newEstimate.notes = this.selectedNote
      }
    },
    discounttype(newValue) {
        if(newValue === 'fixed') {
            this.selectFixed()
        } else {
            this.selectPercentage()
        }
    },  
    subtotal(newValue) {
      if (this.newEstimate.discount_type === 'percentage') {
        this.newEstimate.discount_val =
          (this.newEstimate.discount * newValue) / 100
      }
    },
  },

  async created() {
    await this.loadData()
    await this.fetchInitialItems()
    window.hub.$on('newTax', this.onSelectTax)
    if (this.$route.query.customer) {
      this.selectedCustomer = parseInt(this.$route.query.customer)
      this.customerId = parseInt(this.$route.query.customer)
    }
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('estimate', [
      'addEstimate',
      'fetchEstimate',
      'getEstimateNumber',
      'selectCustomer',
      'updateEstimate',
      'resetSelectedNote',
    ]),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('estimateTemplate', ['fetchEstimateTemplates']),

    ...mapActions('customFields', ['fetchCustomFields']),

    ...mapActions('customer', ['fetchCustomers']),
    
    selectFixed() {
      if (this.newEstimate.discount_type === 'fixed') {
        return
      }

      this.newEstimate.discount_val = Math.round(
        this.newEstimate.discount * 100
      )
      this.newEstimate.discount_type = 'fixed'
    },

    selectPercentage() {
      if (this.newEstimate.discount_type === 'percentage') {
        return
      }
      this.newEstimate.discount_val =
        (this.subtotal * this.newEstimate.discount) / 100

      this.newEstimate.discount_type = 'percentage'
    },

    updateTax(data) {
      Object.assign(this.newEstimate.taxes[data.index], { ...data.item })
    },
    
    openTaxModal() {
        this.$refs.taxmodal.show()
    },

    async fetchInitialItems() {
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
        this.resetSelectedNote(),
        this.fetchEstimateTemplates(),
        this.getEstimateNumber(),
        this.fetchCompanySettings(['estimate_auto_generate']),
      ])
        .then(async ([res1, res2, res3, res4, res5]) => {
          if (
            !this.isEdit &&
            res5.data &&
            res5.data.estimate_auto_generate === 'YES'
          ) {
            if (res4.data) {
              this.estimateNumAttribute = res4.data.nextNumber
              this.estimatePrefix = res4.data.prefix
            }
          } else {
            this.estimatePrefix = res4.data.prefix
          }

          this.isLoadingData = false
        })
        .catch((error) => {
          console.log(error)
        })
    },

    async loadData() {
      if (this.isEdit) {
        this.isLoadingEstimate = true
        
        Promise.all([
          this.fetchEstimate(this.$route.params.id),
          this.fetchCustomFields({
            type: 'Estimate',
            limit: 'all',
          }),
          this.fetchTaxTypes({ limit: 'all' }),
        ])
          .then(async ([res1, res2]) => {
            if (res1.data) {
              this.selectedCustomer = res1.data.estimate.user_id
              this.customerId = res1.data.estimate.user_id
              this.newEstimate = res1.data.estimate
              this.formData = { ...this.formData, ...res1.data.estimate }
              this.newEstimate.estimate_date = moment(
                res1.data.estimate.estimate_date).format(
                'YYYY-MM-DD'
              )
              this.newEstimate.expiry_date = moment(
                res1.data.estimate.expiry_date).format(
                'YYYY-MM-DD'
              )
              this.discountPerItem = res1.data.estimate.discount_per_item
              this.discounttype = res1.data.estimate.discount_type
              this.taxPerItem = res1.data.estimate.tax_per_item
              this.selectedCurrency = this.defaultCurrency
              this.estimateTemplates = res1.data.estimate.estimateTemplates
              this.estimateNumAttribute = res1.data.nextEstimateNumber
              this.estimatePrefix = res1.data.estimatePrefix
              let fields = res1.data.estimate.fields

              if (res2.data) {
                let customFields = res2.data.customFields.data
                this.setEditCustomFields(fields, customFields)
              }
            }

            this.isLoadingEstimate = false

            return
          })
          .catch((error) => {
            console.log(error)
          })
        return true
      }

      this.isLoadingEstimate = true
      await this.setInitialCustomFields('Estimate')
      await this.fetchTaxTypes({ limit: 'all' })
      this.selectedCurrency = this.defaultCurrency
      this.newEstimate.estimate_date = this.$utils.currentDate()
      this.newEstimate.expiry_date = moment().add(7, 'days').format('YYYY-MM-DD')
      this.isLoadingEstimate = false
    },

    openTemplateModal() {
      this.openModal({
        title: this.$t('general.choose_template'),
        componentName: 'EstimateTemplate',
        data: this.getEstimateTemplates,
      })
    },

    addItem() {
      this.newEstimate.items.push({
        ...EstimateStub,
        id: Guid.raw(),
        taxes: [{ ...TaxStub, id: Guid.raw() }],
      })
    },

    removeItem(index) {
      this.newEstimate.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.newEstimate.items[data.index], { ...data.item })
    },

    async submitForm() {
      let validate = await this.touchCustomField()
      if (!this.checkValid() || validate.error) {
        return false
      }

      this.isLoading = true
      this.newEstimate.estimate_number =
        this.estimatePrefix + '-' + this.estimateNumAttribute

      let data = {
        ...this.formData,
        ...this.newEstimate,
        estimate_date: moment(this.newEstimate.estimate_date).format(
          'YYYY-MM-DD'
        ),
        expiry_date: moment(this.newEstimate.expiry_date).format('YYYY-MM-DD'),
        sub_total: this.subtotal,
        total: this.total,
        tax: this.totalTax,
        user_id: null,
        estimate_template_id: this.getTemplateId,
      }

      if (this.selectedCustomer != null) {
        data.user_id = this.selectedCustomer
      }

      if (this.$route.name === 'estimates.edit') {
        this.submitUpdate(data)
        return
      }

      this.submitCreate(data)
    },

    submitCreate(data) {
      this.addEstimate(data)
        .then((res) => {
          if (res.data) {
            this.$router.push(`/admin/estimates/${res.data.estimate.id}/view`)
            window.toastr['success'](this.$t('estimates.created_message'))
          }

          this.isLoading = false
        })
        .catch((err) => {
          this.isLoading = false
        })
    },

    submitUpdate(data) {
      this.updateEstimate(data)
        .then((res) => {
          this.isLoading = false
          if (res.data) {
            this.$router.push(`/admin/estimates/${res.data.estimate.id}/view`)
            window.toastr['success'](this.$t('estimates.updated_message'))
          }
        })
        .catch((err) => {
          this.isLoading = false
        })
    },

    checkItemsData(index, isValid) {
      this.newEstimate.items[index].valid = isValid
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

      this.newEstimate.taxes.push({
        ...TaxStub,
        id: Guid.raw(),
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_type_id: selectedTax.id,
        amount,
      })

    },

    removeEstimateTax(index) {
      this.newEstimate.taxes.splice(index, 1)
    },

    checkValid() {
      this.$v.newEstimate.$touch()
      this.$v.selectedCustomer.$touch()
      this.$v.estimateNumAttribute.$touch()
      window.hub.$emit('checkItems')
      let isValid = true
      this.newEstimate.items.forEach((item) => {
        if (!item.valid) {
          isValid = false
        }
      })
      if (
        !this.$v.selectedCustomer.$invalid &&
        !this.$v.estimateNumAttribute.$invalid &&
        this.$v.newEstimate.$invalid === false &&
        isValid === true
      ) {
        return true
      }
      return false
    },
    onSelectNote(data) {
      this.newEstimate.notes = '' + data.notes
      this.$refs.notePopup.close()
    },
  },
}
</script>
<style lang="scss">
.estimate-create-page {
  .estimate-foot {
    .estimate-total {
      min-width: 390px;
    }
  }

  @media (max-width: 480px) {
    .estimate-foot {
      .estimate-total {
        min-width: 384px !important;
      }
    }
  }
}
</style>
