<template>
  <div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  data() {
    return {
      component: '',
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalActive',
      'modalTitle',
      'componentName',
      'modalSize',
      'modalData',
      'variant',
    ]),
  },
  watch: {
    componentName(component) {
      if (!component) {
        return
      }
      this.component = component
    },
    modalActive(status) {
      if (status) {
        this.$refs.baseModal.open()
        return true
      }
      this.$refs.baseModal.close()
      return false
    },
  },
  methods: {
    ...mapActions('modal', ['openModal', 'closeModal']),
  },
}
</script>
