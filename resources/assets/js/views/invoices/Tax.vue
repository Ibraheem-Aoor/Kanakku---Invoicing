<template>
  <div class="flex items-center justify-between mb-3">
   <label class="pr-2 mb-0" align="right">
        {{ $t('estimates.tax') }}
      </label>
    <div class="flex items-center text-base" style="flex: 4;display:flex;">
     
      <select v-model="selecttax" class="select form-control" style="width:140px;">
          <option v-for="item in filteredTypes" :key="item.id" :disabled="taxData.tax_type_id===item.id" :value="item.id">{{ item.name}} - {{ item.percent}}%</option>
      </select>
    
        <div slot="afterList" style="display: flex;align-items: center;">
          <button
            type="button" class="btn btn-primary" style="margin: 0 10px;"
            @click="openTaxModal"
          >
            <label style="margin:0;">{{
              $t('estimates.add_new_tax')
            }}</label>
          </button>
              <div class="text-sm text-right" style="flex: 3">
      <div v-html="$utils.formatMoney(taxAmount, currency)" />
    </div>
    <div class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer">
      <trash-icon
        v-if="taxes.length && index !== taxes.length - 1"
        class="h-5 text-gray-700"
        icon="trash-alt"
        @click="removeTax(index)"
      />
    </div>
        </div>
      <br />
    </div>
    <TaxTypeModal ref="modal" />
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CheckCircleIcon, TrashIcon } from '@vue-hero-icons/solid'
import TaxTypeModal from '../../components/base/modal/TaxTypeModal'
export default {
  components: {
    CheckCircleIcon,
    TrashIcon,
    TaxTypeModal,
  },
  props: {
    index: {
      type: Number,
      required: true,
    },
    taxData: {
      type: Object,
      required: true,
    },
    taxes: {
      type: Array,
      default: [],
    },
    total: {
      type: Number,
      default: 0,
    },
    totalTax: {
      type: Number,
      default: 0,
    },
    currency: {
      type: [Object, String],
      required: true,
    },
  },
  data() {
    return {
      tax: { ...this.taxData },
      selectedTax: null,
      selecttax:  null,
    }
  },
  computed: {
    ...mapGetters('taxType', ['taxTypes']),
    filteredTypes() {
      const clonedTypes = this.taxTypes.map((a) => ({ ...a }))

      return clonedTypes.map((taxType) => {
        let found = this.taxes.find((tax) => tax.tax_type_id === taxType.id)

        if (found) {
          taxType.$isDisabled = true
        } else {
          taxType.$isDisabled = false
        }

        return taxType
      })
    },
    taxAmount() {
      if (this.tax.compound_tax && this.total) {
        return Math.round(
          ((this.total + this.totalTax) * this.tax.percent) / 100
        )
      }

      if (this.total && this.tax.percent) {
        return Math.round((this.total * this.tax.percent) / 100)
      }

      return 0
    },
  },
  watch: {
    total: {
      handler: 'updateTax',
    },
    totalTax: {
      handler: 'updateTax',
    },
     selecttax(newValue) {
         this.selectedTax = this.taxTypes.find(
        (_type) => _type.id === newValue
      )
      if(this.selectedTax) {
          this.onSelectTax(this.selectedTax)
      }
     }
  },
  created() {
    if (this.taxData.tax_type_id > 0) {
       this.selecttax = this.taxTypes.find(
        (_type) => _type.id === this.taxData.tax_type_id
      ).id
      this.selectedTax = this.taxTypes.find(
        (_type) => _type.id === this.taxData.tax_type_id
      )
    }

    this.updateTax()
    window.hub.$on('newTax', (val) => {
      if (!this.selectedTax) {
        this.selecttax = val.id
        this.selectedTax = val
        this.onSelectTax(val)
      }
    })
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    customLabel({ name, percent }) {
      return `${name} - ${percent}%`
    },
    onSelectTax(val) {
      this.tax.percent = val.percent
      this.tax.tax_type_id = val.id
      this.tax.compound_tax = val.compound_tax
      this.tax.name = val.name

      this.updateTax()
    },
    updateTax() {
      if (this.tax.tax_type_id === 0) {
        return
      }

      this.$emit('update', {
        index: this.index,
        item: {
          ...this.tax,
          amount: this.taxAmount,
        },
      })
    },
    removeTax(index) {
      this.$emit('remove',index, this.tax)
    },
    openTaxModal() {
      this.$refs.modal.show()
    },
  },
}
</script>
