<template>
  <!-- Payment mode Modal -->
					 <div >
           <div id="add_tax" class="modal custom-modal fade" role="dialog">
						<div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">{{$t('settings.customization.payments.add_payment_mode')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" id="cancelbutton" aria-label="Close" @click="closePaymentModeModal">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form @submit.prevent="submitPaymentMode">
                      <div class="form-group">
                        <label>{{$tc('settings.customization.payments.mode_name')}}<span class="text-danger">*</span></label>
                        <input class="form-control" ref="name" v-model="formData.name" :class="{'is-invalid': $v.formData.name.$error}"  @input="$v.formData.name.$touch()" type="text">
                        <div class="invalid-feedback" v-if="$v.formData.name.$error">{{nameError}}</div>
                      </div>
                      
                      <div class="submit-section" >
                        <button class="btn btn-primary" @click="closePaymentModeModal"> <i class="fas fa-window-close" />
                          {{$t('general.cancel')}}</button>
                          <button :disabled="isLoading" class="btn btn-primary"><i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                           {{ !isEdit ? $t('general.save') : $t('general.update') }}</button>
                      </div>
                      
									</form>
								</div>
							</div>
						</div>
					</div>
    <button type="button" id="openmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#add_tax">
</button>
  </div>
  <!-- /Payment mode modal.-->
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
    ...mapActions('payment', ['addPaymentMode', 'updatePaymentMode']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset()
    },
      show() {
         this.formData = {
          id: null,
          name: null,
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
    async submitPaymentMode() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let response

      if (this.isEdit) {
        response = await this.updatePaymentMode(this.formData)
        if (response.data) {
          window.toastr['success'](
            this.$t('settings.customization.payments.payment_mode_updated')
          )
          this.refreshData ? this.refreshData() : ''
          this.$emit("paymentadded");
          this.closePaymentModeModal()
          return true
        }
        window.toastr['error'](response.data.error)
      } else {
        try {
          response = await this.addPaymentMode(this.formData)
          if (response.data) {
            this.isLoading = false
            window.toastr['success'](
              this.$t('settings.customization.payments.payment_mode_added')
            )
            this.refreshData ? this.refreshData() : ''
            this.$emit("paymentadded");
            this.closePaymentModeModal()
            return true
          }
          window.toastr['error'](response.data.error)
        } catch (err) {
          this.isLoading = false
        }
      }
    },
    // async setData(data) {
    //   this.formData = {
    //     id: this.modalData.id,
    //     name: this.modalData.name,
    //   }
    // },
    async setData(data) {
      this.formData = {
        id: data.id,
        name: data.name,
      }
    },
    closePaymentModeModal() {
       document.getElementById("cancelbutton").click()
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
