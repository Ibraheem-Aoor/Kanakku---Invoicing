<template>
      <!-- Add Tax Modal -->
        <div >
           <div id="add_tax" class="modal custom-modal fade" role="dialog">
						<div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">{{ $t('settings.tax_types.add_new_tax') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" id="modalcancelbutton" aria-label="Close" @click="closeTaxModal">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form @submit.prevent="submitTaxTypeData">
                      <div class="form-group">
                        <label class="float-left">{{$t('tax_types.name')}}<span class="text-danger">*</span></label>
                        <input class="form-control" v-model="formData.name" :class="{'is-invalid': $v.formData.name.$error}"  @input="$v.formData.name.$touch()" type="text">
                        <div class="invalid-feedback" v-if="$v.formData.name.$error">{{nameError}}</div>
                      </div>
                      <div class="form-group">
                        <label class="float-left">{{$t('tax_types.percent')}}(%) <span class="text-danger">*</span></label>
                        <input class="form-control" v-model="formData.percent" @keypress="removeSymbol" :class="{'is-invalid': $v.formData.percent.$error}"  @input="$v.formData.percent.$touch()" v-money="defaultInput"  type="text">
                        <div class="invalid-feedback" v-if="$v.formData.percent.$error">{{percentError}}</div>
                      </div>
                      <div class="form-group">
                        <label class="float-left">{{$t('tax_types.description')}}</label>
                        <textarea class="form-control" v-model="formData.description" @input="$v.formData.description.$touch()" type="text" rows="3" columns="5"> </textarea>
                      </div>
                      <div class="submit-section" >
                        <button :loading="isLoading" class="btn btn-primary" @click="closeTaxModal"> <i class="fas fa-window-close" />
                          {{ $t('general.cancel') }}</button>
                          <button :disabled="isLoading" class="btn btn-primary"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                          {{ !isEdit ? $t('general.save') : $t('general.update') }}</button>
                      </div>
									</form>
								</div>
							</div>
						</div>
        </div>
        <button type="button" id="openmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#add_tax"></button>
      </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minLength,
  between,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
        percent: 0,
        description: null,
        compound_tax: false,
        collective_tax: 0,
      },
      defaultInput: {
        decimal: '.',
        thousands: ',',
        prefix: '',
        precision: 2,
        masked: false,
      },
      showModal: false
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      } else {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
    percentError() {
      if (!this.$v.formData.percent.$error) {
        return ''
      }

      if (!this.$v.formData.percent.required) {
        return this.$t('validation.required')
      } 
      else {
        return this.$t('validation.enter_valid_tax_rate')
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      percent: {
        required,
        between: between(0, 100),
      },
      description: {
        maxLength: maxLength(255),
      },
    },
  },
  async mounted() {
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('taxType', ['addTaxType', 'updateTaxType', 'fetchTaxType']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
        percent: 0,
        description: null,
        collective_tax: 0,
      }
      this.$v.formData.$reset()
    },
     show() {
         this.formData = {
          id: null,
          name: null,
          percent: 0,
          description: null,
          collective_tax: 0,
         }
        this.isLoading = false
       this.isEdit = false
       document.getElementById("openmodal").click()
     },
     edit(data) {
         this.isEdit = true
         this.setData(data)
         document.getElementById("openmodal").click()
     },
     removeSymbol() {
       if(this.formData.percent.includes('%')) {
         this.formData.percent = this.formData.percent.replace('%','') 
       } 
       if(this.formData.percent.toString().includes(",")) {
          this.formData.percent = this.formData.percent.replace(/\,/g,'');
       }
     },
    async submitTaxTypeData() {
      
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let response
      if (!this.isEdit) {
        response = await this.addTaxType(this.formData)
      } else {
        response = await this.updateTaxType(this.formData)
      }
      if (response.data) {
        if (!this.isEdit) {
          window.toastr['success'](
            this.$t('settings.tax_types.created_message')
          )
        } else {
          window.toastr['success'](
            this.$t('settings.tax_types.updated_message')
          )
        }
        this.closeTaxModal()
        window.hub.$emit('newTax', response.data.taxType)
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    async setData(data) {
      this.formData = {
        id: data.id,
        name: data.name,
        percent: data.percent,
        description: data.description,
        compound_tax: data.compound_tax ? true : false,
      }
    },
    closeTaxModal() {
      document.getElementById("modalcancelbutton").click();
      this.resetModalData()
      this.resetFormData()
    },
  },
}
</script>
<style scoped>
.switch {
    position: relative;
    width: 40px;
    height: 23px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
    content: "";
    height: 15px;
    width: 15px;
    left: -2px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>