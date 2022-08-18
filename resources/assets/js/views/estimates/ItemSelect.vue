<template>
  <div>
    <select ref="baseSelect" class="select form-control" :class="{'is-invalid': invalid}" v-model="itemSelect" @change="onTextChange">
        <option v-for="item in items" :key="item.id" :value="item.id">{{item.name}}</option>
    </select>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { XCircleIcon, ShoppingCartIcon } from '@vue-hero-icons/solid'
const { maxLength, required  } = require('vuelidate/lib/validators')

export default {
  components: {
    XCircleIcon,
    ShoppingCartIcon,
  },
  props: {
    item: {
      type: Object,
      required: true,
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false,
    },
    invalidDescription: {
      type: Boolean,
      required: false,
      default: false,
    },
    taxPerItem: {
      type: String,
      default: '',
    },
    taxes: {
      type: Array,
      default: null,
    },
  },
  data() {
    return {
      itemSelect: this.item && this.item.item_id || null,
      loading: false,
    }
  },
  validations() {
    return {
      item: {
        description: {
          maxLength: maxLength(255),
        },
      },
      itemSelect: {
        required
      }
    }
  },
  computed: {
    ...mapGetters('item', ['items']),
  },
  watch: {
    invalidDescription(newValue) {
      
    },
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('item', ['fetchItems']),
    async searchItems(search) {
      let data = {
        search,
        filter: {
          name: search,
          unit: '',
          price: '',
        },
        orderByField: '',
        orderBy: '',
        page: 1,
      }

      if (this.item) {
        data.item_id = this.item.item_id
      }

      this.loading = true

      await this.fetchItems(data)

      this.loading = false
    },
    onTextChange() {
      let selectedItem = this.items.find(item => item.id === this.itemSelect)
      //this.searchItems(selectedItem)
      this.$emit('select', selectedItem)
      //this.$emit('search', selectedItem)
    },
    created(){
      if(this.$route.params.id) {
        this.itemSelect = this.item && this.item.id
      }
    },
    onSelect(val) {
      this.$emit('select', val)
      this.fetchItems()
    },
    deselectItem() {
      this.itemSelect = null
      this.$emit('deselect')
    },
  },
}
</script>
