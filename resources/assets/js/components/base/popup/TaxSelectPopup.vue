<template>
    <!-- Add Tax Modal -->
  <div >
    <div id="tax_select_popup" class="modal custom-modal fade" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Tax</h5>
            </div>
            <div class="modal-body">
                <form>
                  <div class="form-group">
                      <label class="float-left">Search</label>
                      <input class="form-control"  v-model="textSearch"  type="text">
                  </div>
                  <div v-if="filteredTaxType.length > 0">
                      <ul class="taxselect">
                        <li v-for="(taxType, index) in filteredTaxType" :key="index">
                            <span   :class="{
                              'pointer-events': taxes.find(
                              (val) => {
                              return val.tax_type_id === taxType.id
                              }
                              ),
                              }" @click="selectTaxType(index,taxType.id)">{{ taxType.name}} {{ taxType.percent}}%</span>
                        </li>
                      </ul>
                  </div>
                  <div v-else >
                      <label class="m-0">{{ $t('general.no_tax_found') }}</label>
                  </div>
                  <div class="submit-section" >
                      <a class="btn btn-primary" @click="openTaxModal">{{ $t('settings.tax_types.add_new_tax') }}</a>
                      <a class="btn btn-primary" id="cancelbutton" data-dismiss="modal" @click="textSearch=null">Cancel</a>
                  </div>
                </form>
            </div>
          </div>
      </div>
    </div>
    <button type="button" id="opentaxmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#tax_select_popup">
    </button>
    <TaxTypeModal ref="modal"/>
  </div>
    
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CheckCircleIcon } from '@vue-hero-icons/solid'
import TaxTypeModal from '../modal/TaxTypeModal'
export default {
  components: {
    CheckCircleIcon,
    TaxTypeModal
  },
  props: {
    taxes: {
      type: Array,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      textSearch: null,
    }
  },
  computed: {
    ...mapGetters('taxType', ['taxTypes']),
    filteredTaxType() {
      if (this.textSearch) {
        var textSearch = this.textSearch
        return this.taxTypes.filter(function (el) {
          return el.name.toLowerCase().indexOf(textSearch.toLowerCase()) !== -1
        })
      } else {
        return this.taxTypes
      }
    },
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    selectTaxType(index, id) {
      const taxType = this.taxTypes.find(item => item.id === id)
      this.$emit('select', taxType)
      document.getElementById("cancelbutton").click();
    },
    openTaxModal() {
      document.getElementById("cancelbutton").click();
      this.$refs.modal.show();
    },
    show() {
      document.getElementById("opentaxmodal").click();
      this.textSearch = null
    }
  },
}
</script>
