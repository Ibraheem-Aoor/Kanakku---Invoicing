<template>
  <div>
      <div class="text-right">
        <a  @click="addItemUnit" class="btn btn-outline-primary">{{ $t('settings.customization.items.add_item_unit') }}</a>
      </div>

    <div class="card-body">
                  <div class="table-responsive">
                     <vuetable ref="vuetable"
                        :api-mode="false"
                        :fields="fields"
                        :per-page="perPage"
                        pagination-path="pagination"
                        :sort-order="sortOrder"
                        @vuetable:pagination-data="onPaginationData"
                        :no-data-template="showNomessage ? 'No records available' : ''"    
                        :data-manager="dataManager"
                        :css="css.table"
                      >
                    <template slot="name" slot-scope="props">
                          {{props.rowData.name}}
                    </template>
                    <template :slot="$t('settings.tax_types.action')" slot-scope="props">
                      <div class="table-button-container">
                           <a  @click="editItemUnit(props.rowData)" class="btn btn-sm btn-white text-success mr-2"><i class="far fa-edit mr-1"></i> {{ $t('general.edit') }}</a> 
                            <a  @click="removeItemUnit(props.rowData.id)" class="btn btn-sm btn-white text-danger mr-2"><i class="far fa-trash-alt mr-1"></i>{{ $t('general.delete') }}</a>
                      </div>
                    </template>
                  </vuetable>
                  <vuetable-pagination ref="pagination"
                    @vuetable-pagination:change-page="onChangePage" :css="css.pagination"
                  ></vuetable-pagination>
                  </div>
                </div>
                <ItemUnitModal ref="modal"/> 
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'
import ItemUnitModal from '../../../components/base/modal/ItemUnitModal'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import TableStyle from '../../../tablestyle/tablestyle'

export default {
  components: {
    TrashIcon,
    PlusIcon,
    PencilIcon,
    ItemUnitModal,
  },
  data() {
      return {
          itemsList: [],
          perPage: 5,
          page: 1,
          totalCount: 0,
          sortOrder: [
            {
                field: 'created_at',
                sortField: 'created_at',
                direction: 'desc'
            }
          ],
          css: TableStyle,
          showNomessage: false,
          fields: [
          { 
            name: 'name', 
            title: this.$tc('settings.customization.items.unit_name'),
            sortField: 'name',
          },
            this.$t('settings.tax_types.action')
          ],
      }
  },
  watch: {
    itemsList(newVal, oldVal) {
      this.$refs.vuetable.refresh();
    },
    sortOrder(newval,oldVal) {
      this.fetchData(this.page=1,[{sortField: newval && newval[0].sortField || 'created_at',direction: newval && newval[0].direction || 'desc'}])
    },
  },
  created() {
      this.getItems()
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('item', ['deleteItemUnit', 'fetchItemUnits']),

    async fetchData( page, sort ) {
      let data = {
        orderByField: sort && sort[0].sortField || 'created_at',
        orderBy: sort && sort[0].direction || 'desc',
        page,
      }
      this.showNoMessage = false
      let response = await this.fetchItemUnits(data)
      this.itemsList = response.data.units.data
      this.totalCount = response.data.units.total
      if(response.data.units.data.length === 0) this.showNoMessage = true
     
    },
    getItems() {
        this.fetchData(this.page=1,'created_at')
    },
     addItemUnit() {
      this.$refs.modal.show()
    },
    onPaginationData (paginationData) {
     this.$refs.pagination.setPaginationData(paginationData)
    },
    async onChangePage (page) {
      this.page = page;
      await this.fetchData(page,this.sortOrder);
      this.$refs.vuetable.changePage(page)
    },
    dataManager(sortOrder, pagination) {
      let local = this.itemsList;
      
      // sortOrder can be empty, so we have to check for that as well
      if (sortOrder.length > 0) {
        local = _.orderBy(
          local,
          sortOrder[0].sortField,
          sortOrder[0].direction
        );
      }
      
      pagination = this.$refs.vuetable.makePagination(
        this.totalCount,
        this.perPage
      );
     
      return {
        pagination: pagination,
        data: local
      };
    },
    async editItemUnit(data) {
      this.$refs.modal.edit(data)
    },

    async removeItemUnit(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.items.item_unit_confirm_delete'),
       
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteItemUnit(id)

          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.items.deleted_message')
            )
           this.getItems()
            return true
          }
          window.toastr['error'](
            this.$t('settings.customization.items.already_in_use')
          )
        }
      })
    },
  },
}
</script>
