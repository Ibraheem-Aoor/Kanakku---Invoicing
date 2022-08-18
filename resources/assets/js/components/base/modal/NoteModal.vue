<template>
  <!-- Add Category Modal -->
					 <div >
           <div id="add_note_modal" class="modal custom-modal fade" role="dialog">
						<div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Add Note</h5>
                    <button type="button" class="close" data-dismiss="modal" id="cancelbutton" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form @submit.prevent="submitNote">
                      <div class="form-group">
                        <label>{{$t('settings.customization.notes.name')}}<span class="text-danger">*</span></label>
                        <input class="form-control" ref="name" :class="{ 'is-invalid': $v.formData.name.$error }"  v-model="formData.name" @input="$v.formData.name.$touch()"  type="text">
                        <div class="invalid-feedback"  v-if="$v.formData.name.$error">{{nameError}}</div>
                      </div>
                       <div class="form-group">
                        <label>{{$t('settings.customization.notes.type')}}<span class="text-danger">*</span></label>
                         <select class="select form-control"  :class="{ 'is-invalid': $v.noteType.$error }"  v-model="noteType" @change="$v.noteType.$touch()">
                          <option>Select</option>
                          <option v-for="(item,index) in types" :key="index" :value="item">{{item}}</option>
                        </select>
                        <div class="invalid-feedback" v-if="$v.noteType.$error">{{typeError}}</div>
                        
                      </div>
                      <div class="form-group">
                        <label>{{$t('settings.customization.notes.notes')}}<span class="text-danger">*</span></label>
                          <!-- <base-custom-input
                            v-model="formData.notes"
                            :fields="fields"
                            class="mt-2"
                          /> -->
                          <textarea class="form-control"  v-model="formData.notes"> </textarea>
                      </div>
                      <div class="submit-section" >
                        <button class="btn btn-light submit-btn" @click="closeNoteModal"> <i class="fas fa-window-close" />
                          Cancel</button>
                      </div>
                      <div class="submit-section" >
                        <button :disabled="isLoading"  class="btn btn-primary submit-btn"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                            {{ !isEdit ? $t('general.save') : $t('general.update') }}</button>
                      </div>
									</form>
								</div>
							</div>
						</div>
					</div>
    <button  id="openmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#add_note_modal"></button>
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
      types: ['Invoice', 'Estimate', 'Payment'],
      selectType: null,
      formData: {
        type: '',
        name: '',
        notes: '',
      },
      noteType: null,
      fields: [],
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
    noteError() {
      if (!this.$v.formData.notes.$error) {
        return ''
      }

      if (!this.$v.formData.notes.required) {
        return this.$tc('validation.required')
      }
    },
    typeError() {
      if (!this.$v.noteType.$error) {
        return ''
      }

      if (!this.$v.noteType.required) {
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
      notes: {
        required,
      },
    },
    noteType: {
      required,
    },
  },
  async mounted() {
    this.setFields()
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    } else {
      this.modalData
        ? (this.noteType = this.modalData)
        : (this.noteType = 'Invoice')
    }
  },
  watch: {
    noteType() {
      this.setFields()
    },
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('notes', ['addNote', 'updateNote']),
    ...mapActions('invoice', {
      setInvoiceNote: 'selectNote',
    }),
    ...mapActions('estimate', {
      setEstimateNote: 'selectNote',
    }),
    ...mapActions('payment', {
      setPaymentNote: 'selectNote',
    }),

    setFields() {
      this.fields = ['customer', 'customerCustom']

      if (this.noteType === 'Invoice') {
        this.fields.push('invoice', 'invoiceCustom')
      }

      if (this.noteType === 'Estimate') {
        this.fields.push('estimate', 'estimateCustom')
      }

      if (this.noteType === 'Payment') {
        this.fields.push('payment', 'paymentCustom')
      }

      return true
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
    resetFormData() {
      this.formData = {
        name: null,
        notes: null,
      }

      this.notetype = null
      this.$v.formData.$reset()
    },
    async submitNote() {
      this.$v.formData.$touch()
      this.$v.noteType.$touch()
     
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true

      if (this.isEdit) {
        let data = {
          id: this.modalDataID,
          type: this.noteType,
          name: this.formData.name,
          notes: this.formData.notes,
        }

        let res = await this.updateNote(data)
        if (res.data) {
          window.toastr['success'](
            this.$t('settings.customization.notes.note_updated')
          )

          this.refreshData ? this.refreshData() : ''
          this.closeNoteModal()
          return true
        }
        window.toastr['error'](res.data.error)
      } else {
        try {
          let data = {
            type: this.noteType,
            name: this.formData.name,
            notes: this.formData.notes,
          }

          let response = await this.addNote(data)

          if (response.data && response.data.note) {
            this.isLoading = false
            window.toastr['success'](
              this.$t('settings.customization.notes.note_added')
            )
            if (
              (this.$route.name === 'invoices.create' &&
                response.data.note.type === 'Invoice') ||
              (this.$route.name === 'invoices.edit' &&
                response.data.note.type === 'Invoice')
            ) {
              this.setInvoiceNote(response.data.note)
            }

            if (
              (this.$route.name === 'estimates.create' &&
                response.data.note.type === 'Estimate') ||
              (this.$route.name === 'estimates.edit' &&
                response.data.note.type === 'Estimate')
            ) {
              this.setEstimateNote(response.data.note)
            }

            if (
              (this.$route.name === 'payments.create' &&
                response.data.note.type === 'Payment') ||
              (this.$route.name === 'payments.edit' &&
                response.data.note.type === 'Payment')
            ) {
              this.setPaymentNote(response.data.note)
            }

            this.refreshData ? this.refreshData() : ''
            this.closeNoteModal()
            return true
          }
          window.toastr['error'](response.data.error)
        } catch (err) {
          if (err.response.data.errors.name) {
            this.isLoading = true
          }
        }
      }
    },
    async setData() {
      this.noteType = this.modalData.type
      this.formData.name = this.modalData.name
      this.formData.notes = this.modalData.notes
    },
    closeNoteModal() {
      document.getElementById("cancelbutton").click()
      this.closeModal()
      this.resetFormData()
    },
  },
}
</script>
<style lang="scss">
.note-modal {
  .header-editior .editor-menu-bar {
    margin-left: 0.5px;
    margin-right: 0px;
  }
}
</style>
