<template>
        <tr>
            <td >
                 <item-select
                  ref="itemSelect"
                  :item="item"
                  :tax-per-item="taxPerItem"
                  :invalid="$v.item.name.$error"
                  :taxes="item.taxes"
                  @search="searchVal"
                  @select="onSelectItem"
                  @onSelectItem="isSelected = true"
                />
            </td>
            <td>
              <input v-model="item.quantity" :class="{ 'is-invalid': $v.item.quantity.$error }" type="text"  @keyup="updateItem" @input="$v.item.quantity.$touch()" class="form-control">
               <div v-if="$v.item.quantity.$error">
                <span v-if="!$v.item.quantity.maxLength" class="text-danger">
                  {{ $t('validation.quantity_maxlength') }}
                </span>
              </div>
            </td>
            <td>
              <input v-model="price" type="text" v-money="money" :class="{ 'is-invalid': $v.item.price.$error }" @input="$v.item.price.$touch()" class="form-control">
               <div v-if="$v.item.price.$error">
                    <span v-if="!$v.item.price.maxLength" class="text-danger">
                      {{ $t('validation.price_maxlength') }}
                    </span>
                  </div>
            </td>
             <td v-if="discountPerItem === 'YES'">
              <div class="row float-right">
                      <div class="col-md-6"> <input type="text"  v-model="discount" :class="{ 'is-invalid': $v.item.discount_val.$error }" @input="$v.item.discount_val.$touch()" class="form-control"/></div>
                      <div class="col-md-6"> 
                        <select class="form-control" v-model="discounttype">
                          <option value="fixed">{{currency && currency.symbol || defaultCurrency }}</option>
                          <option value="percentage">%</option>
                        </select>
                      </div>
                </div>
            </td>
            <td>
              <span>
                  <div v-html="$utils.formatMoney(total, currency)" />
                </span>
            </td>
            <td v-if="taxPerItem === 'YES'">
              
              <tax
                v-for="(tax, index) in item.taxes"
                :key="tax.id"
                :index="index"
                :tax-data="tax"
                :taxes="item.taxes"
                :discounted-total="total"
                :total-tax="totalSimpleTax"
                :total="total"
                :currency="currency"
                @update="updateTax"
                @remove="removeTax"
              />
            </td>
            <td class="add-remove text-right">
              <i class="fas fa-plus-circle" @click="addNewItem"></i >  <i v-if="index != 0" class="fas fa-minus-circle" @click="removeItem"></i>  
            </td>
          </tr>
         
</template>
<script>
import Guid from 'guid'
import { mapGetters } from 'vuex'
import TaxStub from '../../stub/tax'
import EstimateStub from '../../stub/estimate'
import ItemSelect from './ItemSelect'
import Tax from './Tax'
import { TrashIcon, ChevronDownIcon } from '@vue-hero-icons/solid'

const {
  required,
  minValue,
  between,
  maxLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    Tax,
    ItemSelect,
    TrashIcon,
    ChevronDownIcon,
  },

  props: {
    itemData: {
      type: Object,
      default: null,
    },
    index: {
      type: Number,
      default: null,
    },
    type: {
      type: String,
      default: '',
    },
    currency: {
      type: [Object, String],
      required: true,
    },
    taxPerItem: {
      type: String,
      default: '',
    },
    discountPerItem: {
      type: String,
      default: '',
    },
    estimateItems: {
      type: Array,
      required: true,
    },
    taxes: {
      type: Array,
      default: null,
    },
  },
  data() {
    return {
      isClosePopup: false,
      itemSelect: null,
      item: { ...this.itemData },
      maxDiscount: 0,
      price: 0.00,
      money: {
        decimal: '.',
        thousands: ',',
        prefix: this.currency && this.currency.symbol,
        precision: 2,
        masked: false,
      },
      isSelected: false,
      discounttype: 'fixed',
    }
  },
  computed: {
    ...mapGetters('item', ['items']),
    ...mapGetters('modal', ['modalActive']),
    ...mapGetters('company', ['defaultCurrencyForInput']),
    customerCurrency() {
      if (this.currency) {
        return {
          decimal: this.currency.decimal_separator,
          thousands: this.currency.thousand_separator,
          prefix: this.currency.symbol + ' ',
          precision: this.currency.precision,
          masked: false,
        }
      } else {
        return this.defaultCurrencyForInput
      }
    },
    isShowRemoveItemIcon() {
      if (this.estimateItems.length == 1) {
        return false
      }
      return true
    },
    subtotal() {
      return this.item.price * this.item.quantity
    },
    discount: {
      get: function () {
        return this.item.discount
      },
      set: function (newValue) {
        if (this.item.discount_type === 'percentage') {
          this.item.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.item.discount_val = Math.round(newValue * 100)
        }

        this.item.discount = newValue
      },
    },
    total() {
      return this.subtotal - this.item.discount_val
    },
    totalSimpleTax() {
      return Math.round(
        window._.sumBy(this.item.taxes, function (tax) {
          if (!tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },
    totalCompoundTax() {
      return Math.round(
        window._.sumBy(this.item.taxes, function (tax) {
          if (tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },
    totalTax() {
      return this.totalSimpleTax + this.totalCompoundTax
    },
    // price: {
    //   get: function () {
    //     if (parseFloat(this.item.price) > 0) {
    //       return this.item.price / 100
    //     }

    //     return this.item.price
    //   },
    //   set: function (newValue) {
    //     if (parseFloat(newValue) > 0) {
    //       this.item.price = Math.round(newValue * 100)
    //       this.maxDiscount = this.item.price
    //     } else {
    //       this.item.price = newValue
    //     }
    //   },
    // },
  },
  mounted() {
     this.money = {
        decimal: '.',
        thousands: ',',
        prefix: this.currency && this.currency.symbol+' ' || '$ ',
        precision: 2,
        masked: false,
      }
      const data = {...this.itemData}
      this.discounttype = data.discount_type
  },
  watch: {
    item: {
      handler: 'updateItem',
      deep: true,
    },
    price: function(newValue){
        if(newValue && !_.isNaN(newValue) ){
          if(newValue.toString().includes(this.money.prefix)) {
              newValue = newValue.replace(this.money.prefix,'');
          } if(newValue.toString().includes(",")) {
              newValue = newValue.replace(/\,/g,'');
          } else {
              newValue = newValue
          }
          
           if (parseFloat(newValue) > 0) {
              this.item.price = Math.round(newValue * 100)
              this.maxDiscount = this.item.price
            } else {
              this.item.price = newValue
            }
        }
    },
    subtotal(newValue) {
      if (this.item.discount_type === 'percentage') {
        this.item.discount_val = (this.item.discount * newValue) / 100
      }
    },
    discounttype(newValue) {
        if(newValue === 'fixed') {
            this.selectFixed()
        } else {
            this.selectPercentage()
        }
    },  
    currency(newValue) {
        this.money.prefix = this.currency && this.currency.symbol+' ' || '$'
    },
    modalActive(val) {
      if (!val) {
        this.isSelected = false
      }
    },
  },
  validations() {
    return {
      item: {
        name: {
          required,
        },
        quantity: {
          required,
          minValue: minValue(0),
          maxLength: maxLength(20),
        },
        price: {
          required,
          minValue: minValue(1),
          maxLength: maxLength(20),
        },
        discount_val: {
          between: between(0, this.maxDiscount),
        },
        description: {
          maxLength: maxLength(65000),
        },
      },
    }
  },
  
  created() {
    window.hub.$on('checkItems', this.validateItem)
    window.hub.$on('newItem', (val) => {
      if (this.taxPerItem === 'YES') {
        this.item.taxes = val.taxes
      }
      if (!this.item.item_id && this.modalActive && this.isSelected) {
        this.onSelectItem(val)
      }
    })
    if (parseFloat(this.item.price) > 0) {
         this.price = this.item.price / 100
    } else {
        this.price =  this.item.price
    }
  },
  // mounted() {
  //   this.$v.item.$reset()
  // },
  methods: {
    updateTax(data) {
      this.$set(this.item.taxes, data.index, data.item)

      let lastTax = this.item.taxes[this.item.taxes.length - 1]

      if (lastTax.tax_type_id !== 0) {
        this.item.taxes.push({ ...TaxStub, id: Guid.raw() })
      }

      this.updateItem()
    },
    addNewItem() {
      this.$emit('addItem')
    },
    removeTax(index) {
      this.item.taxes.splice(index, 1)
      this.updateItem()
    },
    taxWithPercentage({ name, percent }) {
      return `${name} (${percent}%)`
    },
    searchVal(val) {
      this.item.name = val
    },
    deselectItem() {
      this.item = {
        ...EstimateStub,
        id: this.item.id,
        taxes: [{ ...TaxStub, id: Guid.raw() }],
      }
      this.$nextTick(() => {
        this.$refs.itemSelect.$refs.baseSelect.$refs.search.focus()
      })
    },
    onSelectItem(item) {
      this.item.name = item.name
      this.price = item.price
      this.item.price = item.price
      this.item.item_id = item.id
      this.item.description = item.description
      this.item.unit_name = item.unit_name
      if (this.taxPerItem === 'YES' && item.taxes) {
        let index = 0
        item.taxes.forEach((tax) => {
          this.updateTax({ index, item: { ...tax } })
          index++
        })
      }
    },
    selectFixed() {
      if (this.item.discount_type === 'fixed') {
        return
      }

      this.item.discount_val = Math.round(this.item.discount * 100)
      this.item.discount_type = 'fixed'
    },
    selectPercentage() {
      if (this.item.discount_type === 'percentage') {
        return
      }

      this.item.discount_val = (this.subtotal * this.item.discount) / 100

      this.item.discount_type = 'percentage'
    },
    updateItem() {
      this.$emit('update', {
        index: this.index,
        item: {
          ...this.item,
          total: this.total,
          totalSimpleTax: this.totalSimpleTax,
          totalCompoundTax: this.totalCompoundTax,
          totalTax: this.totalTax,
          tax: this.totalTax,
          taxes: [...this.item.taxes],
        },
      })
    },
    removeItem() {
      this.$emit('remove', this.index)
    },
    validateItem() {
      this.$v.item.$touch()

      if (this.item !== null) {
        this.$emit('itemValidate', this.index, !this.$v.$invalid)
      } else {
        this.$emit('itemValidate', this.index, false)
      }
    },
  },
}
</script>
