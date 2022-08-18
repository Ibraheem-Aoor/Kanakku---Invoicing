<template>
 <base-page>
          <div class="page-header">
            <div class="row">
              <div class="col-sm-6">
                <h3 class="page-title">Settings</h3>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><router-link to="/admin/dashboard">Dashboard</router-link>
                  </li>
                  <li class="breadcrumb-item active">Profile Settings</li>
                </ul>
              </div>
            </div>
          </div>
        
          <div class="row">
           <div class="col-xl-3 col-md-4">
            
              <!-- Settings Menu -->
              <div class="widget settings-menu">
                <ul>
                  <li class="nav-item">
                    <router-link to="/admin/settings/user-profile" class="nav-link">
                      <i class="far fa-user"></i> <span>Account Settings</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/company-info" class="nav-link">
                      <i class="far fa-list-alt"></i> <span>Company Information</span>
                    </router-link>
                  </li>
                    <li class="nav-item">
                    <router-link to="/admin/settings/customization" class="nav-link">
                      <i class="fas fa-edit"></i> <span>{{$t('settings.menu_title.customization')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/preferences" class="nav-link">
                      <i class="fas fa-cog"></i> <span>Preferences</span>
                    </router-link>
                  </li>
               
                   <li class="nav-item">
                    <router-link to="/admin/settings/notifications" class="nav-link">
                      <i class="far fa-bell"></i> <span>Notifications</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/tax-types" class="nav-link">
                      <i class="far fa-check-square"></i> <span>Tax Types</span>
                    </router-link>
                  </li>
                   <li class="nav-item">
                    <router-link to="/admin/settings/payment-mode" class="nav-link">
                      <i class="far fa-bell"></i> <span>Payment Modes</span>
                    </router-link>
                  </li>
                   <li class="nav-item">
                    <router-link to="/admin/settings/custom-fields" class="nav-link">
                      <i class="far fa-bell"></i> <span>Custom Fields</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/notes" class="nav-link">
                      <i class="far fa-check-square"></i> <span>Notes</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/expense-category" class="nav-link">
                      <i class="far fa-list-alt"></i> <span>Expense Categories</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/mail-configuration" class="nav-link">
                      <i class="far fa-check-square"></i> <span>Mail Configuration</span>
                    </router-link>
                  </li>
                </ul>
              </div>
              <!-- /Settings Menu -->
            </div>
            
              <div class="col-xl-9 col-md-8">
                <div class="card">
                  <div class="card-header">
                    <div class="page-header">
                      <div class="row align-items-center">
                        <div class="col">
                          <h5 class="card-title">Notes</h5>
                            <p>Save time by creating notes and reusing them on your invoices, estimates & payments.</p>
                        </div>
                        <div class="col-auto">
                          <button  @click="openNoteSelectModal" class="btn btn-outline-primary btn-sm">Add Note</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
           <NoteModal  ref="modal"/>
          </div>
    </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'
import NoteModal from '../../components/base/modal/NoteModal'
export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
    NoteModal
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('notes', ['fetchNotes', 'deleteNote']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchNotes(data)

      return {
        data: response.data.notes.data,
        pagination: {
          totalPages: response.data.notes.last_page,
          currentPage: page,
          count: response.data.notes.count,
        },
      }
    },
    
    removeNote(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.notes.note_confirm_delete'),
       
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteNote(id)

          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.notes.deleted_message')
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](
            this.$t('settings.customization.notes.already_in_use')
          )
        }
      })
    },

    editNote(data) {
      this.openModal({
        title: this.$t('settings.customization.notes.edit_note'),
        componentName: 'NoteSelectModal',
        id: data.id,
        data: data,
        variant: 'lg',
        refreshData: this.$refs.table.refresh,
      })
    },

    openNoteSelectModal() {
      this.$refs.modal.show();
    },
  },
}
</script>
