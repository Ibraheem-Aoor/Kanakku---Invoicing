<template>
  <!-- Add Unit Modal -->
					 <div >
           <div id="add_unit" class="modal custom-modal fade" role="dialog">
						<div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">{{ $t('general.add_new_item') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" id="cancelbutton" aria-label="Close" @click="closeItemUnitModal">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form @submit.prevent="submitItemUnit">
                      <div class="form-group">
                        <label class="float-left">{{$t('tax_types.name')}}<span class="text-danger">*</span></label>
                        <input class="form-control" ref="name" v-model="formData.name" :class="{'is-invalid': $v.formData.name.$error}"  @input="$v.formData.name.$touch()" type="text">
                        <div class="invalid-feedback" v-if="$v.formData.name.$error">{{nameError}}</div>
                      </div>
                     
                      <div class="submit-section" >
                        <button :loading="isLoading" class="btn btn-primary" @click="closeItemUnitModal"> <i class="fas fa-window-close" />
                          {{ $t('general.cancel') }}</button>
                          <button :loading="isLoading" class="btn btn-primary"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                          {{ !isEdit ? $t('general.save') : $t('general.update') }}</button>
                      </div>
                      
									</form>
								</div>
							</div>
						</div>
					</div>
    <button type="button" id="openmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#add_unit">
</button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
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
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(2),
      },
    },
  },

  async mounted() {
    this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('item', ['addItemUnit', 'updateItemUnit', 'fatchItemUnit']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset()
    },
    async submitItemUnit() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      let response

      try {
        if (!this.isEdit) {
          response = await this.addItemUnit(this.formData)
        } else {
          response = await this.updateItemUnit(this.formData)
        }

        if (response.data) {
          this.isLoading = false
          if (!this.isEdit) {
            window.toastr['success'](
              this.$t('settings.customization.items.item_unit_added')
            )
          } else {
            window.toastr['success'](
              this.$t('settings.customization.items.item_unit_updated')
            )
          }
           this.closeItemUnitModal()
          return true
        }
      } catch (error) {
        this.isLoading = false
        window.toastr['error'](response.data.error)
      }
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
    async setData(data) {
      this.formData = {
        id: data.id,
        name: data.name,
      }
    },
    closeItemUnitModal() {
      this.resetModalData()
      this.resetFormData()
      document.getElementById("cancelbutton").click()

    },
  },
}
</script>
