<template>
  <div class="flex-app">
     <div class="container">
      <input type="text" :placeholder="$t('global_search.search')" v-model="name"  @blur="toggle = false" @click="toggle = true" class="form-control">
      <button class="btn" type="submit"><i class="fas fa-search"></i></button>
      <div class="results scrollbar"  v-if="toggle">
         <span class="bg-gray" v-if="getCustomerList.length > 0">CUSTOMERS</span>
          <div v-if="getCustomerList.length > 0">
        <div class="result ml-3 mt-1" v-for="d in getCustomerList" :key="d.id">
          <span @click="selectCustomer(d.id,1)" @mousedown.prevent>{{ d.name }}</span>
        </div>
        </div>
         <span v-if="getUserList.length > 0" class="bg-gray search_name">USERS</span>
          <div v-if="getUserList.length > 0">
            <div class="result ml-3 mt-1" v-for="ds in getUserList" :key="ds.id">
              <span @click="selectCustomer(ds.id,2)" @mousedown.prevent>{{ ds.name }}</span>
            </div>
          </div>
          <div>
            <span class="bg-gray" v-if="getCustomerList.length === 0 && getUserList.length === 0"> No users found</span>
          </div>
      </div>
       </div>
  </div>
</template>

<script>
import { SearchIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import _ from 'lodash'

export default {
  components: {
    SearchIcon,
  },
  data() {
    return {
      isShow: false,
      isLoading: false,
      name: '',
      toggle: false,
    }
  },

  computed: {
    ...mapGetters('search', ['getCustomerList', 'getUserList']),
  },

  created() {
    this.searchUsers()
  },
  watch: {
    name: _.debounce(async function () {
      this.isLoading = true
      await this.searchUsers({ search: this.name }).then(() => {
        this.isShow = true
      })
      if (this.name === '') {
        this.isShow = false
      }
      this.isLoading = false
    }, 500),

    
  },
  methods: {
    ...mapActions('search', ['searchUsers']),

    throttledMethod: _.debounce(async function () {
      this.isLoading = true
      await this.searchUsers({ search: this.name }).then(() => {
        this.isShow = true
      })
      if (this.name === '') {
        this.isShow = false
      }
      this.isLoading = false
    }, 500),

    initGenerator(name) {
      if (name) {
        let nameSplit = name.split('')
        let initials =
          nameSplit[0].charAt(0).toUpperCase() +
          nameSplit[1].charAt(0).toUpperCase()
        return initials
      }
    },
    selectCustomer(id,type) {
      if(type === 1) {
        this.$router.push(`/admin/customers/${id}/view`)
      } else {
        this.$router.push(`/admin/users/${id}/edit`)
      }
      
      this.toggle= false
    }
  },
}
</script>

<style scoped>
.box::-webkit-scrollbar {
  width: 4px;
}

.box::-webkit-scrollbar-thumb {
  background-color: transparent;
  outline: 1px solid white;
  border-radius: 0.42rem !important;
}

.box::-webkit-scrollbar-thumb {
  background-color: #e4e6ef;
}
  .flex-app {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
  }
  .container {
    justify-content: center;
    align-items: center;
    width: 200px;
    margin-top: 50px;
    position: relative;
  }
 .container .results {
    position: absolute;
    width: 230px !important;
    background: #fff;  box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
   height: 135px;
    overflow: auto;
  }
 
  .container .inputtype {
      width: 100%;
      border: none;
      height: 38px;
      padding-left: 10px;
      padding-right: -10px;
      box-shadow: inset 0 0 0 1px #b2b2b2;
      border-radius: 4px;
      background: transparent;
      transition: all .2s ease;
  }
  .scrollbar {
    height: 150px;
  
    border: 6px solid #FFFF;
    border-radius: 5px;
  }
</style>