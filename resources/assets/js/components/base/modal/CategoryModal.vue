<template>
   <!-- Add Category Modal -->
      <div >
        <div id="add_category_modal" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" id="cancelbutton" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form @submit.prevent="submitCategoryData">
                  <div class="form-group">
                    <label>{{$t('expenses.category')}} <span class="text-danger">*</span></label>
                    <input class="form-control" ref="name" :class="{ 'is-invalid': $v.formData.name.$error }"  v-model="formData.name" @input="$v.formData.name.$touch()"  type="text">
                    <div class="invalid-feedback" v-if="$v.formData.name.$error">{{nameError}}</div>
                  </div>
                  <div class="form-group">
                    <label>{{$t('expenses.description')}}</label>
                    <textarea class="form-control" v-model="formData.description" @input="$v.formData.description.$touch()" type="text"> </textarea>
                    <div class="invalid-feedback" v-if="$v.formData.description.$error">{{descriptionError}}</div>
                  </div>
                  <div class="submit-section" >
                    <button :loading="isLoading" class="btn btn-primary" @click="closeCategoryModal"> <i class="fas fa-window-close" />
                      {{$t('general.cancel')}}</button>
                    <button :loading="isLoading" class="btn btn-primary"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>{{ !isEdit ? $t('general.save') : $t('general.update') }}</button>
                  </div>
                </form>
              </div>
          </div>
        </div>
      </div>
      <button type="button" id="openmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#add_category_modal"></button>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
        description: null,
      },
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }
      if (!this.$v.formData.name.maxLength) {
        return this.$tc('validation.description_maxlength')
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      description: {
        maxLength: maxLength(255),
      },
    },
  },
  watch: {
    modalDataID(val) {
      if (val) {
        this.isEdit = true
        this.setData()
      } else {
        this.isEdit = false
      }
    },
    modalActive(val) {
      if (!this.modalActive) {
        this.resetFormData()
      }
    },
  },

  mounted() {
    this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },

  methods: {
    ...mapActions('modal', ['closeModal']),
    ...mapActions('category', ['addCategory', 'updateCategory']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
        description: null,
      }
      this.$v.formData.$reset()
    },
    show() {
        this.isEdit = false
        document.getElementById("openmodal").click()
    },
    edit(data) {
        this.isEdit = true
        this.setData(data)
        document.getElementById("openmodal").click()
    },
    async submitCategoryData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let response
      if (!this.isEdit) {
        response = await this.addCategory(this.formData)
      } else {
        response = await this.updateCategory(this.formData)
      }

      if (response.data) {
        if (!this.isEdit) {
          window.toastr['success'](
            this.$t('settings.expense_category.created_message')
          )
        } else {
          window.toastr['success'](
            this.$t('settings.expense_category.updated_message')
          )
        }
        window.hub.$emit('newCategory', response.data.category)
        this.refreshData ? this.refreshData() : ''
        this.closeCategoryModal()
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
  
    async setData(data) {
      this.formData = {
        id: data.id,
        name: data.name,
        description: data.description,
      }
    },
    
    closeCategoryModal() {
      document.getElementById("cancelbutton").click()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
