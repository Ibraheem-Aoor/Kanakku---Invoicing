<template>
  <!-- Add Custom Field Modal -->
					 <div >
           <div id="add_custom_field" class="modal custom-modal fade" role="dialog">
						<div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Add Custom Field</h5>
                    <button type="button" class="close" data-dismiss="modal" id="cancelbutton" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form @submit.prevent="submitCustomFieldData">
                      <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input class="form-control" v-model="formData.name"  :class="{ 'is-invalid': $v.formData.name.$error }" ref="name" @input="$v.formData.name.$touch()" type="text">
                        <div class="invalid-feedback" v-if="$v.formData.name.$error">{{nameError}}</div>
                      </div>
                      <div class="form-group">
                        <label>Modal<span class="text-danger">*</span></label>
                        <!-- <input class="form-control" v-model="formData.name"  type="text"> -->
                        <select class="select form-control" v-model="formData.model_type" @input="$v.formData.model_type.$touch()" :class="{ 'is-invalid': $v.formData.model_type.$error }">
                          <option>Select</option>
                          <option v-for="(item,index) in modelTypes" :key="index" :value="item">{{item}}</option>
                        </select>
                        <div class="invalid-feedback" v-if="$v.formData.model_type.$error">{{modalTypeError}}</div>
                      </div>
                       <div class="form-group">
                        <label>Required<span class="text-danger">*</span></label>
                        <label class="switch ml-2">
                          <input type="checkbox" v-model="formData.is_required"  @click="formData.is_required=!formData.is_required">
                          <span class="slider round"></span>
                        </label>
                      </div>
                      <div class="form-group">
                        <label>Type<span class="text-danger">*</span></label>
                         <select class="select form-control" v-model="selectType" @change="onSelectTypeChange">
                          <option>Select</option>
                          <option v-for="(item,index) in dataTypes" :key="index" :value="item.value">{{item.label}}</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Label</label>
                        <input class="form-control" v-model="formData.label" :class="{ 'is-invalid': $v.formData.label.$error}" @input="$v.formData.label.$touch()" type="text" />
                        <div class="invalid-feedback" v-if="$v.formData.label.$error">{{labelError}}</div>
                      </div>
                       <div class="form-group">
                        <label>Default value</label>
                        <input class="form-control" v-model="formData.default_answer" type="text" />
                      </div>
                     
                      <div class="form-group">
                        <label>Placeholder</label>
                        <input class="form-control" v-model="formData.placeholder" type="text"  />
                      </div>
                      <div class="form-group">
                        <label>Order</label>
                        <input class="form-control" v-model="formData.order" :class="{ 'is-invalid': $v.formData.order.$error}" @input="$v.formData.order.$touch()" type="number" />
                        <div class="invalid-feedback" v-if="$v.formData.order.$error">{{orderError}}</div>
                      </div>
                      <div class="submit-section" >
                        <button :loading="isLoading" class="btn btn-primary" @click="closeCategoryModal"> <i class="fas fa-window-close" />
                          Cancel</button>
                          <button :disabled="isLoading" class="btn btn-primary"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                            {{ !isEdit ? $t('general.save') : $t('general.update') }}</button>
                      </div>
                     
									</form>
								</div>
							</div>
						</div>
					</div>
    <button type="button" id="openmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#add_custom_field">
</button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { MinusCircleIcon } from '@vue-hero-icons/solid'

import moment from 'moment'

const {
  required,
  requiredIf,
  numeric,
  minLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    MinusCircleIcon,
  },
  data() {
    return {
      isEdit: false,
      dateType: 'custom',
      isLoading: false,
      relativeDateTypes: [
        'Today',
        'Tomorrow',
        'Yesterday',
        'Starting Date of Week',
        'Ending Date of Week',
        'Starting Date of Next Week',
        'Ending Date of Next Week',
        'Starting Date of Previous Week',
        'Ending Date of Previous Week',
        'Starting Date of Month',
        'Ending Date of Month',
        'Starting Date of Next Month',
        'Ending Date of Next Month',
        'Starting Date of Previous Month',
        'Ending Date of Previous Month',
        'Starting Date of Fiscal Month',
        'Ending Date of Fiscal Month',
      ],
      dataTypes: [
        { label: 'Text', value: 'Input' },
        { label: 'Textarea', value: 'TextArea' },
        { label: 'Phone', value: 'Phone' },
        { label: 'URL', value: 'Url' },
        { label: 'Number', value: 'Number' },
        { label: 'Select Field', value: 'Dropdown' },
        { label: 'Switch Toggle', value: 'Switch' },
        { label: 'Date', value: 'Date' },
        { label: 'Time', value: 'Time' },
        { label: 'Date & Time', value: 'DateTime' },
      ],
      modelTypes: ['Customer', 'Invoice', 'Estimate', 'Expense', 'Payment'],
      selectType: null,
      formData: {
        label: null,
        type: null,
        name: null,
        default_answer: null,
        is_required: false,
        placeholder: null,
        model_type: null,
        order: 1,
        options: [],
      },
    }
  },
  validations: {
    selectType: {
      required,
    },
    formData: {
      name: {
        required,
      },
      label: {
        required,
      },
      model_type: {
        required,
      },
      order: {
        required,
        numeric,
      },
      options: {
        required: requiredIf('isDropdownSelected'),
        minLength: minLength(1),
        $each: {
          name: {
            required: requiredIf('isDropdownSelected'),
            minLength: minLength(1),
          },
        },
      },
    },
  },
  computed: {
    ...mapGetters('modal', ['modalData', 'modalDataID', 'refreshData']),
    isDropdownSelected() {
      if (this.selectType && this.selectType.label === 'Select Field') {
        return true
      }
      return false
    },
    isSwitchTypeSelected() {
      if (this.selectType && this.selectType.label === 'Switch Toggle') {
        return true
      }
      return false
    },
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    labelError() {
      if (!this.$v.formData.label.$error) {
        return ''
      }

      if (!this.$v.formData.label.required) {
        return this.$tc('validation.required')
      }
    },
    modalTypeError() {
      if (!this.$v.formData.model_type.$error) {
        return ''
      }

      if (!this.$v.formData.model_type.required) {
        return this.$tc('validation.required')
      }
    },
    dataTypeError() {
      if (!this.$v.selectType.$error) {
        return ''
      }

      if (!this.$v.selectType.required) {
        return this.$tc('validation.required')
      }
    },
    hasPlaceHolder() {
      if (this.selectType.label == 'Switch Toggle') {
        return false
      }
      return true
    },
    orderError() {
      if (!this.$v.formData.order.$error) {
        return ''
      }

      if (!this.$v.formData.order.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.order.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
  },
  watch: {
    dateType(newValue, oldvalue) {
      if (oldvalue != null || oldvalue != undefined) {
        this.onChangeReset()
      }
    },
  },
  mounted() {
    if (this.modalDataID) {
      this.setData()
      return true
    }
    this.formData.model_type = this.modelTypes[0]
    this.selectType = this.dataTypes[0].value
    this.formData.type = this.dataTypes[0].value
  },
  methods: {
    ...mapActions('customFields', [
      'addCustomField',
      'updateCustomField',
      'fetchCustomField',
    ]),
    ...mapActions('modal', ['closeModal','resetModalData']),
    resetFormData() {
      this.formData = {
        label: null,
        label: null,
        type: null,
        dateTimeValue: null,
        default_answer: null,
        is_required: false,
        placeholder: null,
        model_type: null,
        options: [{ name: '' }],
      }
      this.$v.$reset()
    },
    async submitCustomFieldData() {
      this.$v.selectType.$touch()
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      let data = {
        ...this.formData,
        options: this.formData.options.map((option) => option.name),
        default_answer:
          this.isDropdownSelected && this.formData.default_answer
            ? this.formData.default_answer.name
            : this.formData.default_answer,
      }
      if (this.isSwitchTypeSelected && this.formData.default_answer == null) {
        data.default_answer = false
      }
      if (data.type == 'Date') {
        data.default_answer = data.default_answer
          ? moment(data.default_answer).format('YYYY-MM-DD')
          : null
      }
      if (data.type == 'Time' && typeof data.default_answer == 'object') {
        let HH =
          data && data.default_answer && data.default_answer.HH
            ? data.default_answer.HH
            : null
        let mm =
          data && data.default_answer && data.default_answer.mm
            ? data.default_answer.mm
            : null
        let ss =
          data && data.default_answer && data.default_answer.ss
            ? data.default_answer.ss
            : null
        data.default_answer = `${HH}:${mm}:${ss}`
      }
      let response = null
      if (this.isEdit) {
        this.isLoading = true
        response = await this.updateCustomField(data)
        window.toastr['success'](
          this.$tc('settings.custom_fields.updated_message')
        )
         this.refreshData ? this.refreshData() : ''
        this.closeCategoryModal()
        this.$emit("customfieldsadded");
        return true
      }
      
      this.isLoading = true
      response = await this.addCustomField(data)
      
      window.toastr['success'](this.$tc('settings.custom_fields.added_message'))
      this.refreshData ? this.refreshData() : ''
      this.closeCategoryModal()
      this.$emit("customfieldsadded");
      return true
    },
    show() {
          this.formData = {
            label: null,
            label: null,
            type: this.dataTypes && this.dataTypes[0].value,
            dateTimeValue: null,
            default_answer: null,
            is_required: false,
            placeholder: null,
            model_type: this.modelTypes && this.modelTypes[0],
            options: [{ name: '' }],
          }
          this.isLoading = false
          this.isEdit = false
          this.selectType = this.dataTypes && this.dataTypes[0].value
         document.getElementById("openmodal").click()
    },
    edit(data) {
        this.isEdit = true
        this.setData(data)
        document.getElementById("openmodal").click()
    },
    addNewOptions(option) {
      this.formData.options = [{ name: option }, ...this.formData.options]
    },
    removeOption(index) {
      this.formData.options.splice(index, 1)
    },
    // async setData() {
    //   let response = await this.fetchCustomField(this.modalDataID)
    //   let fieldData = response.data.customField
    //   this.isEdit = true
    //   let data = {
    //     ...fieldData,
    //     options: [],
    //     dateTimeValue: fieldData.defaultAnswer,
    //     default_answer: fieldData.defaultAnswer,
    //     options: fieldData.options
    //       ? fieldData.options.map((option) => {
    //           return { name: option ? option : '' }
    //         })
    //       : [],
    //   }
    //   this.selectType = this.dataTypes.find(
    //     (type) => type.value == fieldData.type
    //   )
    //   if (data.type == 'Dropdown') {
    //     data.default_answer = { name: fieldData.defaultAnswer }
    //   }
    //   this.formData = { ...data }
    // }, 
     async setData(id) {
      let response = await this.fetchCustomField(id)
      let fieldData = response.data.customField
      this.isEdit = true
      let data = {
        ...fieldData,
        options: [],
        dateTimeValue: fieldData.defaultAnswer,
        default_answer: fieldData.defaultAnswer,
        options: fieldData.options
          ? fieldData.options.map((option) => {
              return { name: option ? option : '' }
            })
          : [],
      }
      this.selectType = fieldData.type && this.dataTypes && this.dataTypes.find(
        (type) => type.value == fieldData.type
      ).value
      if (data.type == 'Dropdown') {
        data.default_answer = { name: fieldData.defaultAnswer }
      }
      this.formData = { ...data }
    },
    onChangeReset() {
      this.formData = {
        ...this.formData,
        default_answer: null,
        is_required: false,
        placeholder: null,
        options: [],
      }
    },
    onSelectTypeChange() {
      this.formData.type = this.selectType;
      this.$v.selectType.$touch()
    },
    closeCategoryModal() {
      document.getElementById("cancelbutton").click();
      this.resetFormData()
      this.resetModalData()
      this.closeModal()
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