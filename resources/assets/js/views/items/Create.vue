<template>
    <base-page>
      <div class="row align-items-center">
      <div class="col">
          <h3 class="page-title">{{$t('items.new_item')}}</h3>
          <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <router-link to="/admin/dashboard">{{$t('general.home')}}</router-link>
            </li>
            <li class="breadcrumb-item">
                <router-link to="/admin/items">{{$tc('items.item', 2)}}</router-link>
            </li>
            <li class="breadcrumb-item" v-if="$route.name === 'items.edit'">{{$t('items.edit_item')}}</li>
            <li class="breadcrumb-item" v-else>{{$t('items.new_item')}}</li>
          </ul>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
          <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
          <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{$tc('items.item', 2)}}</h4>
                <form @submit.prevent="submitItem">
                  <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                            <label>{{$t('items.name')}} <span class="text-danger">*</span></label>
                            <input type="text"  v-model.trim="formData.name" :class="{ 'is-invalid': $v.formData.name.$error }" @input="$v.formData.name.$touch()" autocomplete="off" class="form-control">
                            <div class="invalid-feedback" v-if="$v.formData.name.$error">{{nameError}}</div>
                        </div>
                        <div class="form-group">
                            <label>{{$t('items.price')}} <span class="text-danger">*</span></label>
                            <div class="input-group">
                              <span>
                              </span>
                              <input type="text" v-model.trim="price" :class="{ 'is-invalid': $v.formData.price.$error }" @input="$v.formData.price.$touch()" autocomplete="off"  v-money="money" class="form-control">
                              <div class="invalid-feedback" v-if="priceError">{{priceError}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{$t('items.select_a_unit') }}</label>
                            <select class="select form-control" v-model="formData.unit">
                              <option>Select</option>
                              <option v-for="item in itemUnits" :key="item.id" :value="item.id">{{item.name}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{$t('items.description')}}</label>
                            <input type="text" v-model.trim="formData.description" autocomplete="off" :class="{ 'is-invalid': $v.formData.description.$error }" @input="$v.formData.description.$touch()" class="form-control">
                            <div class="invalid-feedback" v-if="$v.formData.description.$error">{{descriptionError}}</div>
                        </div>
                        <button class="btn btn-primary" :loading="isLoading" :disabled="isLoading" type="submit"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i> {{ isEdit ? $t('items.update_item') : $t('items.save_item') }}</button>
                      </div>
                  </div>
                </form>
            </div>
          </div>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { ShoppingCartIcon } from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  numeric,
  minValue,
  maxLength,
  alpha,
} = require('vuelidate/lib/validators')
  import {VMoney} from 'v-money'

export default {
  components: {
    ShoppingCartIcon,
  },

  data() {
    return {
      isLoading: false,
      title: 'Add Item',
      units: [],
      taxes: [],
      taxPerItem: '',
      formData: {
        name: '',
        description: '',
        price: '',
        unit_id: null,
        unit: null,
        taxes: [],
      },
      isRequestOnGoing: false,
      price: '0.01',
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
    }
  },
  directives: {money: VMoney},
  watch: {
    price: function(newValue){
        if(newValue && !_.isNaN(newValue) ){
            const symbol = newValue && newValue.toString().split(" ")[0]
            if(newValue.toString().includes(symbol)) {
              newValue = newValue.toString().replace(symbol,'');
          } if(newValue.toString().includes(",")) {
              newValue = newValue.toString().replace(/\,/g,'');
          } else {
              newValue = newValue
          }
           if (parseFloat(newValue) > 0) {
              this.formData.price = Math.round(newValue * 100)
            } else {
              this.formData.price = newValue
            }
        }
    },
  },
  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),

    ...mapGetters('item', ['itemUnits']),

    ...mapGetters('taxType', ['taxTypes']),

    pageTitle() {
      if (this.$route.name === 'items.edit') {
        return this.$t('items.edit_item')
      }
      return this.$t('items.new_item')
    },

    ...mapGetters('taxType', ['taxTypes']),

    isEdit() {
      if (this.$route.name === 'items.edit') {
        return true
      }
      return false
    },

    isTaxPerItem() {
      return this.taxPerItem === 'YES' ? 1 : 0
    },

    getTaxTypes() {
      return this.taxTypes.map((tax) => {
        return {
          ...tax,
          tax_type_id: tax.id,
          tax_name: tax.name + ' (' + tax.percent + '%)',
        }
      })
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },

    priceError() {
      if (!this.$v.formData.price.$error) {
        return ''
      }

      if (!this.$v.formData.price.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.price.maxLength) {
        return this.$t('validation.price_maxlength')
      }

      if (!this.$v.formData.price.minValue) {
        return this.$t('validation.price_minvalue')
      }
    },

    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
  },

  created() {
    this.money.prefix = this.defaultCurrencyForInput && this.defaultCurrencyForInput.prefix
    this.loadData()
  },

  mounted() {
    this.setTaxPerItem()

    this.$v.formData.$reset()
  },

  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },

      price: {
        required,
        numeric,
        maxLength: maxLength(20),
        minValue: minValue(0.1),
      },

      description: {
        maxLength: maxLength(65000),
      },
    },
  },

  methods: {
    ...mapActions('item', [
      'addItem',
      'fetchItem',
      'updateItem',
      'fetchItemUnits',
    ]),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    async setTaxPerItem() {
      let response = await this.fetchCompanySettings(['tax_per_item'])

      if (response.data) {
        response.data.tax_per_item === 'YES'
          ? (this.taxPerItem = 'YES')
          : (this.taxPerItem = 'NO')
      }
    },

    async loadData() {
      if (this.isEdit) {
        this.isRequestOnGoing = true
        let response = await this.fetchItem(this.$route.params.id)

        this.formData = { ...response.data.item, unit: null }
        this.fractional_price = response.data.item.price
        this.formData.price = response.data.item.price
        this.price = response.data.item.price
        if (this.formData.unit_id) {
          await this.fetchItemUnits({ limit: 'all' })
          this.formData.unit = response.data.item && response.data.item.unit_id && this.itemUnits.find(
            (_unit) => response.data.item.unit_id === _unit.id
          ).id
        }

        if (this.formData.taxes) {
          await this.fetchTaxTypes({ limit: 'all' })
          this.formData.taxes = response.data.item.taxes.map((tax) => {
            return { ...tax, tax_name: tax.name + '(' + tax.percent + '%)' }
          })
        }
        this.isRequestOnGoing = false
      } else {
        this.isRequestOnGoing = true
        await this.fetchItemUnits({ limit: 'all' })
        await this.fetchTaxTypes({ limit: 'all' })
        this.isRequestOnGoing = false
      }
    },

    async submitItem() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      if (this.formData.unit) {
        this.formData.unit_id = this.formData.unit
      }

      let response
      this.isLoading = true

      if (this.isEdit) {
        response = await this.updateItem(this.formData)
      } else {
        let data = {
          ...this.formData,
          taxes: this.formData.taxes.map((tax) => {
            return {
              tax_type_id: tax.id,
              amount: (this.formData.price * tax.percent) / 100,
              percent: tax.percent,
              name: tax.name,
              collective_tax: 0,
            }
          }),
        }
        response = await this.addItem(data)
      }

      if (response.data) {
        this.isLoading = false

        if (!this.isEdit) {
          window.toastr['success'](this.$tc('items.created_message'))
          this.$router.push('/admin/items')
          return true
        } else {
          window.toastr['success'](this.$tc('items.updated_message'))
          this.$router.push('/admin/items')
          return true
        }
        window.toastr['error'](response.data.error)
      }
    },

    async addItemUnit() {
      this.openModal({
        title: this.$t('settings.customization.items.add_item_unit'),
        componentName: 'ItemUnit',
      })
    },
  },
}
</script>
