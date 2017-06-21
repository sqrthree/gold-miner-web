<template>
  <div class="container applicants">
    <div class="el-tabs">
      <div class="el-tabs__header">
        <div class="el-tabs__nav-wrap">
          <div class="el-tabs__nav">
            <div class="el-tabs__active-bar" style="width: 88px; transform: translateX(0px);"></div>
            <router-link class="el-tabs__item is-active" :to="'/applications/applicants'">译者申请</router-link>
            <router-link class="el-tabs__item" :to="'/applications/texts'">试译文本</router-link>
          </div>
        </div>
      </div>
    </div>
    <el-table :data="applicants" border>
      <el-table-column prop="cdate" label="申请日期"></el-table-column>
      <el-table-column prop="major" label="擅长领域"></el-table-column>
      <el-table-column prop="description" label="英语能力"></el-table-column>
      <el-table-column label="详情">
        <template scope="scope">
          <el-button type="text" size="small"><router-link :to="`/applications/applicants/${scope.row.id}`">查看</router-link></el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'ApplicationUsers',
  data() {
    return {}
  },
  computed: {
    ...mapGetters(['applicants']),
  },
  methods: {
  },
  created() {
    this.$store.dispatch('fetchApplicants')
  },
}
</script>

<style lang="scss" scoped>
.applicants {
  margin-top: 10px;
}
.el-button a {
  color: #20a0ff;
}
</style>
