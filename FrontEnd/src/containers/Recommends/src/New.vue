<template>
  <el-row class="recomment">
    <el-col :span="10" :offset="7">
      <h1 class="text-center">推荐优质英文文章</h1>
      <el-form ref="form" :model="form" label-width="100px">
        <el-form-item label="文章地址" required>
          <el-input v-model="form.url"></el-input>
        </el-form-item>
        <el-form-item label="文章标题" required>
          <el-input v-model="form.title"></el-input>
        </el-form-item>
        <el-form-item label="所属分类" required>
          <el-select v-model="form.category" placeholder="请选择文章所属的类别">
            <el-option v-for="item in categories.id" :key="item" :label="categories.data[item].category" :value="item"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="文章介绍" required>
          <el-input type="textarea" v-model="form.description"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onSubmit">立即推荐</el-button>
        </el-form-item>
      </el-form>
    </el-col>
  </el-row>
</template>

<script>
import { mapState, mapGetters } from 'vuex'

export default {
  name: 'Recommend',
  data() {
    return {
      form: {
        url: '',
        title: '',
        category: '',
        description: '',
      },
    }
  },
  computed: {
    ...mapState(['categories']),
    ...mapGetters(['currentUser']),
  },
  methods: {
    onSubmit() {
      const { url, title, category, description } = this.form

      if (!url || !title || !category || !description) return

      this.$store.dispatch('addRecommend', {
        url,
        title,
        category,
        description,
        recommender: this.currentUser.id,
      }).then(() => {
        this.$message.success('推荐成功，非常感谢你的文章~')
        this.$router.replace('/')
      }).catch(err => this.$message.error(err.message))
    },
  },
  created() {
    this.$store.dispatch('fetchCategories')
  },
}
</script>

<style lang="scss" scoped>
.recomment {
  margin-top: 30px;
}
</style>
